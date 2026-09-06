<?php

namespace Tests\Feature\Security;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Actions\Inventory\AddStockInboundAction;
use App\Enums\ProductStatus;
use App\Enums\VoucherType;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecuritySourceToSinkTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = app(CreateCategoryAction::class)->execute([
            'name' => 'Koleksi Sutra Eksklusif',
            'slug' => 'koleksi-sutra-eksklusif',
            'is_active' => true,
        ]);

        $this->product = app(CreateProductAction::class)->execute([
            'category_id' => $this->category->id,
            'name' => 'Royal Signature Robe',
            'slug' => 'royal-signature-robe',
            'status' => ProductStatus::Active,
            'variants' => [
                [
                    'sku' => 'MLG-ROBE-GLD-L',
                    'title' => 'Gold Obsidian / L',
                    'price' => 500000,
                    'weight_grams' => 400,
                ],
            ],
        ]);

        $this->variant = $this->product->variants->first();

        app(AddStockInboundAction::class)->execute($this->variant->inventoryItem, [
            'quantity' => 20,
        ]);
    }

    /**
     * Temuan 1: Manipulasi Harga Satuan Produk (unit_price) dari Klien Harus Ditolak.
     */
    public function test_arbitrary_client_unit_price_is_ignored_and_catalog_price_is_enforced(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Hacker Pembeli',
                'email' => 'hacker@blackhat.com',
                'phone' => '081299990000',
            ],
            'items' => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity' => 1,
                    'unit_price' => 100, // Mencoba memodifikasi harga dari Rp 500.000 menjadi Rp 100
                ],
            ],
            'shipping_address' => [
                'recipient_name' => 'Hacker Pembeli',
                'phone' => '081299990000',
                'address_line1' => 'Jl. Anonim No. 0',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
            ],
            'shipping_total' => 15000,
        ];

        $response = $this->postJson(route('api.v1.orders.checkout'), $payload);

        $response->assertCreated();

        // Subtotal HARUS tetap Rp 500.000, BUKAN Rp 100!
        $this->assertEquals(500000, $response->json('data.pricing.subtotal'));
        $this->assertEquals(500000, $response->json('data.items.0.unit_price'));

        $this->assertDatabaseHas('orders', [
            'subtotal' => 500000,
            'source' => 'storefront',
        ]);
    }

    /**
     * Temuan 2: Injeksi Diskon Liar (discount_total) Tanpa Voucher Sah Harus Ditolak.
     */
    public function test_arbitrary_client_discount_total_without_voucher_is_discarded(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Diskon Pemburu Liar',
                'email' => 'pemburu@diskon.com',
                'phone' => '081288887777',
            ],
            'items' => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity' => 1,
                ],
            ],
            'shipping_address' => [
                'recipient_name' => 'Diskon Pemburu Liar',
                'phone' => '081288887777',
                'address_line1' => 'Jl. Promo Gratis No. 1',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
            ],
            'discount_total' => 450000, // Mencoba memotong Rp 450.000 tanpa kode voucher
            'shipping_total' => 15000,
        ];

        $response = $this->postJson(route('api.v1.orders.checkout'), $payload);

        $response->assertCreated();

        // Diskon total HARUS 0 karena tidak ada voucher sah
        $this->assertEquals(0, $response->json('data.pricing.discount_total'));
        $this->assertDatabaseHas('orders', [
            'subtotal' => 500000,
            'discount_total' => 0,
        ]);
    }

    /**
     * Temuan 3: PII Masking pada Endpoint Lacak Publik (orders.track).
     */
    public function test_public_tracking_endpoint_masks_customer_pii_for_unauthenticated_requests(): void
    {
        $order = Order::create([
            'order_number' => 'MLG-20260907-889911',
            'source' => 'storefront',
            'subtotal' => 500000,
            'shipping_total' => 15000,
            'grand_total' => 515000,
        ]);

        $order->customer()->associate(Customer::create([
            'name' => 'Bambang Soedirman',
            'email' => 'bambang.soedirman@perusahaan.co.id',
            'phone' => '081234567899',
        ]))->save();

        $order->address()->create([
            'recipient_name' => 'Bambang Soedirman',
            'phone' => '081234567899',
            'address_line1' => 'Jl. Mega Kuningan Barat IX Kav. 14',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12950',
        ]);

        $response = $this->getJson(route('api.v1.orders.track', ['order_number' => $order->order_number]));

        $response->assertOk();

        // Email harus disamarkan (ba***@perusahaan.co.id)
        $email = $response->json('data.customer.email');
        $this->assertStringContainsString('***@', $email);
        $this->assertStringNotContainsString('bambang.soedirman@perusahaan.co.id', $email);

        // Nomor telepon harus disamarkan (0812****899)
        $phone = $response->json('data.customer.phone');
        $this->assertStringContainsString('****', $phone);
        $this->assertStringNotContainsString('081234567899', $phone);

        // Alamat jalan spesifik harus disamarkan
        $addr = $response->json('data.shipping_address.address_line1');
        $this->assertStringContainsString('****', $addr);
    }

    /**
     * Temuan 4: Bypass Voucher Member-Only via Pemalsuan Email Harus Ditolak.
     */
    public function test_unauthenticated_guest_cannot_claim_member_only_voucher_by_spoofing_email(): void
    {
        $member = Customer::create([
            'name' => 'VIP Member Sultan',
            'email' => 'vip.member@malega.id',
            'phone' => '081112223334',
        ]);

        $voucher = Voucher::create([
            'code' => 'MEMBEREXCL30',
            'name' => 'Voucher Eksklusif Member 30rb',
            'type' => VoucherType::FixedAmount,
            'amount' => 30000,
            'min_order_amount' => 100000,
            'allow_guest' => false, // Khusus Member!
            'is_active' => true,
            'is_public' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonths(1),
        ]);

        // Guest mencoba memvalidasi voucher member dengan mengetik email member tanpa Bearer token
        $response = $this->postJson(route('api.v1.vouchers.validate'), [
            'code' => 'MEMBEREXCL30',
            'subtotal' => 200000,
            'email' => 'vip.member@malega.id', // Spoofed email
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertStringContainsString('khusus untuk member', $response->json('message'));
    }

    /**
     * Temuan 5: Open Redirect pada return_url Payment Invoice Ditolak jika Domain Jahat.
     */
    public function test_payment_invoice_rejects_untrusted_phishing_return_url(): void
    {
        $order = Order::create([
            'order_number' => 'MLG-20260907-778899',
            'source' => 'storefront',
            'subtotal' => 500000,
            'shipping_total' => 15000,
            'grand_total' => 515000,
        ]);

        // Mencoba menyusupkan domain phishing luar
        $response = $this->postJson(route('api.v1.payments.invoice'), [
            'order_number' => $order->order_number,
            'return_url' => 'https://evil-phishing-attacker.com/steal-creds',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['return_url']);
    }

    /**
     * Temuan 5b: return_url yang Sah (Domain Malega / Localhost) Diizinkan.
     */
    public function test_payment_invoice_accepts_whitelisted_return_url(): void
    {
        $order = Order::create([
            'order_number' => 'MLG-20260907-778800',
            'source' => 'storefront',
            'subtotal' => 500000,
            'shipping_total' => 15000,
            'grand_total' => 515000,
        ]);

        $order->address()->create([
            'recipient_name' => 'Pembeli Resmi',
            'phone' => '081299991111',
            'address_line1' => 'Jl. Malega No. 1',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12190',
        ]);

        $response = $this->postJson(route('api.v1.payments.invoice'), [
            'order_number' => $order->order_number,
            'return_url' => 'https://store.malega.my.id/order-confirmation',
        ]);

        // Karena Duitku sandbox mock/request diproses, tidak boleh gagal validasi 422
        $this->assertNotEquals(422, $response->getStatusCode());
    }

    /**
     * Peningkatan Entropi Nomor Pesanan (6 digit acak = 1.000.000 kombinasi harian).
     */
    public function test_order_number_has_high_entropy_six_digits(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Tester Entropi',
                'email' => 'entropy@malega.id',
                'phone' => '081299992222',
            ],
            'items' => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity' => 1,
                ],
            ],
            'shipping_address' => [
                'recipient_name' => 'Tester Entropi',
                'phone' => '081299992222',
                'address_line1' => 'Jl. Entropi',
                'city' => 'Jakarta',
                'province' => 'DKI',
                'postal_code' => '10000',
            ],
        ];

        $response = $this->postJson(route('api.v1.orders.checkout'), $payload);

        $response->assertCreated();

        $orderNumber = $response->json('data.order_number');
        // Format MLG-YYYYMMDD-XXXXXX (18 karakter: MLG- (4) + YYYYMMDD (8) + - (1) + 6 digit (6) = 19 karakter)
        $parts = explode('-', $orderNumber);
        $this->assertCount(3, $parts);
        $this->assertEquals('MLG', $parts[0]);
        $this->assertEquals(date('Ymd'), $parts[1]);
        $this->assertEquals(6, strlen($parts[2]), "Digit acak harus 6 digit untuk proteksi entropi tinggi.");
    }
}
