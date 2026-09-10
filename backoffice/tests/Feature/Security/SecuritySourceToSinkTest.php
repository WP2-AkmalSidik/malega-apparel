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

        // Nama pelanggan dan penerima harus disamarkan
        $name = $response->json('data.customer.name');
        $this->assertStringContainsString('***', $name);
        $this->assertStringNotContainsString('Bambang Soedirman', $name);

        $recipient = $response->json('data.shipping_address.recipient_name');
        $this->assertStringContainsString('***', $recipient);
        $this->assertStringNotContainsString('Bambang Soedirman', $recipient);

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

    /**
     * Temuan Remediasi 1: Manipulasi Ongkos Kirim Rp 0 Ditolak / Dikenakan Tarif Standar.
     */
    public function test_storefront_checkout_enforces_standard_shipping_baseline_when_client_sends_zero(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Zero Shipping Attacker',
                'email' => 'zeroship@attacker.com',
                'phone' => '081299993333',
            ],
            'items' => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity' => 1,
                ],
            ],
            'shipping_address' => [
                'recipient_name' => 'Penerima Gratisan',
                'phone' => '081299993333',
                'address_line1' => 'Jl. Pelosok Jauh',
                'city' => 'Merauke',
                'province' => 'Papua Selatan',
                'postal_code' => '99611',
            ],
            'shipping_total' => 0, // Mencoba manipulasi ongkir Rp 0
        ];

        $response = $this->postJson(route('api.v1.orders.checkout'), $payload);

        $response->assertCreated();

        // Ongkos kirim harus dinaikkan ke tarif minimum standar (Rp 15.000)
        $this->assertEquals(15000, $response->json('data.pricing.shipping_total'));
        $this->assertDatabaseHas('orders', [
            'order_number' => $response->json('data.order_number'),
            'shipping_total' => 15000,
        ]);
    }

    /**
     * Temuan Remediasi 2: Akun Pelanggan Tamu (Guest) Tidak Dapat Diklaim Langsung via Register.
     */
    public function test_guest_customer_record_cannot_be_claimed_without_verification(): void
    {
        Customer::create([
            'name' => 'Pelanggan Tamu Sah',
            'email' => 'guest.korban@malega.id',
            'phone' => '081299995555',
            'password' => null, // Dibuat saat checkout tamu
            'is_active' => true,
        ]);

        // Attacker mencoba mendaftarkan akun dengan email korban tamu untuk mencuri data pesanan
        $response = $this->postJson(route('api.v1.customers.register'), [
            'name' => 'Attacker Pembajak',
            'email' => 'guest.korban@malega.id',
            'phone' => '081999996666',
            'password' => 'newattackerpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertStringContainsString('sudah terdaftar', $response->json('message'));
    }

    /**
     * Temuan Remediasi 3: Endpoint Checkout Storefront Memiliki Pembatasan Laju (Throttle).
     */
    public function test_storefront_checkout_endpoint_is_rate_limited(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Flooder',
                'email' => 'flooder@botnet.com',
                'phone' => '081299998888',
            ],
            'items' => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity' => 1,
                ],
            ],
            'shipping_address' => [
                'recipient_name' => 'Flooder',
                'phone' => '081299998888',
                'address_line1' => 'Jl. Flood',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
            ],
            'shipping_total' => 15000,
        ];

        // Eksekusi checkout hingga melebihi kuota 15 req/menit
        $hitRateLimit = false;
        for ($i = 0; $i < 17; $i++) {
            $res = $this->postJson(route('api.v1.orders.checkout'), $payload);
            if ($res->getStatusCode() === 429) {
                $hitRateLimit = true;
                break;
            }
        }

        $this->assertTrue($hitRateLimit, "Checkout harus memicu HTTP 429 ketika dibombardir melebihi limit.");
    }

    /**
     * Temuan Remediasi 4: Akses Riwayat Pesanan Pelanggan Wajib Terautentikasi dan Mengisolasi Objek Antar-Pelanggan (BOLA/IDOR Prevention).
     */
    public function test_customer_orders_endpoint_requires_auth_and_prevents_cross_customer_order_leakage(): void
    {
        // 1. Unauthenticated request harus 401
        $resUnauth = $this->getJson(route('api.v1.customers.orders'));
        $resUnauth->assertStatus(401);

        // 2. Buat Customer A dan Pesanan miliknya
        $customerA = Customer::create([
            'name' => 'Pelanggan A',
            'email' => 'customerA@malega.id',
            'phone' => '081211112222',
            'password' => 'secretA123',
            'remember_token' => 'token_cust_a_12345',
            'is_active' => true,
        ]);

        $orderA = Order::create([
            'order_number' => 'MLG-20260907-111111',
            'customer_id' => $customerA->id,
            'source' => 'storefront',
            'subtotal' => 500000,
            'shipping_total' => 15000,
            'grand_total' => 515000,
        ]);

        // 3. Buat Customer B dan Pesanan rahasia miliknya
        $customerB = Customer::create([
            'name' => 'Pelanggan B Rahasia',
            'email' => 'customerB@malega.id',
            'phone' => '081233334444',
            'password' => 'secretB123',
            'remember_token' => 'token_cust_b_67890',
            'is_active' => true,
        ]);

        $orderB = Order::create([
            'order_number' => 'MLG-20260907-222222',
            'customer_id' => $customerB->id,
            'source' => 'storefront',
            'subtotal' => 1000000,
            'shipping_total' => 20000,
            'grand_total' => 1020000,
        ]);

        // Customer A mengakses endpoint /api/v1/customers/orders
        $resAuthA = $this->withHeader('Authorization', 'Bearer token_cust_a_12345')
            ->getJson(route('api.v1.customers.orders'));

        $resAuthA->assertOk();
        $ordersData = $resAuthA->json('data');

        // Customer A HANYA boleh melihat Order A miliknya
        $orderNumbers = array_column($ordersData, 'order_number');
        $this->assertContains('MLG-20260907-111111', $orderNumbers);
        $this->assertNotContains('MLG-20260907-222222', $orderNumbers, "Customer A TIDAK boleh melihat pesanan Customer B (BOLA/IDOR Tercegah).");
    }

    /**
     * Temuan Remediasi 5: Webhook Biteship Wajib Menolak Payload Beridentifier Kosong/NULL untuk Mencegah Transisi Status Liar.
     */
    public function test_biteship_webhook_rejects_payload_with_null_identifiers_preventing_unauthorized_state_transition(): void
    {
        $order = Order::create([
            'order_number' => 'MLG-20260907-333333',
            'source' => 'storefront',
            'subtotal' => 500000,
            'shipping_total' => 15000,
            'grand_total' => 515000,
        ]);

        $shipment = \App\Models\Shipment::create([
            'order_id' => $order->id,
            'courier_company' => 'jne',
            'courier_service_name' => 'reg',
            'waybill_id' => 'JNE9988776655',
            'biteship_order_id' => null,
            'biteship_tracking_id' => null,
            'status' => 'confirmed',
            'shipment_fee' => 15000,
        ]);

        // Attacker mengirim webhook kosong atau hanya berisi status=delivered tanpa identifier
        $response = $this->postJson(route('api.v1.webhooks.biteship'), [
            'status' => 'delivered',
            'order_id' => null,
            'courier_tracking_id' => null,
            'courier_waybill_id' => null,
        ]);

        // Harus ditolak dengan status 400
        $response->assertStatus(400);

        // Status shipment dan order TIDAK boleh berubah menjadi delivered / completed
        $this->assertEquals('confirmed', $shipment->fresh()->status);
        $this->assertNotEquals(\App\Enums\OrderStatus::Completed, $order->fresh()->order_status);
    }

    /**
     * SEC-NEW-01: Inactive or suspended customer cannot submit or access reviews.
     */
    public function test_inactive_or_banned_customer_cannot_post_review_or_fetch_reviews(): void
    {
        $bannedCustomer = Customer::create([
            'name' => 'Banned User',
            'email' => 'banned@test.com',
            'phone' => '081299990001',
            'password' => 'secret123',
            'is_active' => false,
            'remember_token' => 'mlg_cust_banned_token_123',
        ]);

        $order = Order::create([
            'order_number' => 'MLG-20260910-999001',
            'customer_id' => $bannedCustomer->id,
            'source' => 'storefront',
            'order_status' => \App\Enums\OrderStatus::Processing,
            'payment_status' => \App\Enums\PaymentStatus::Paid,
            'subtotal' => 399000,
            'grand_total' => 414000,
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'product_name' => 'Test Product',
            'variant_title' => 'Default',
            'sku' => 'SKU-001',
            'unit_price' => 399000,
            'quantity' => 1,
            'subtotal' => 399000,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer mlg_cust_banned_token_123')
            ->postJson(route('api.v1.products.reviews.store', $this->product->id), [
                'rating' => 5,
                'headline' => 'Banned user attempt',
                'review' => 'This review should be rejected by 403.',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('product_reviews', [
            'customer_id' => $bannedCustomer->id,
        ]);

        $reviewsResponse = $this->withHeader('Authorization', 'Bearer mlg_cust_banned_token_123')
            ->getJson(route('api.v1.customers.reviews'));

        $reviewsResponse->assertStatus(403);
    }

    /**
     * SEC-NEW-02: Review submission enforces server-authoritative order_id and prevents IDOR spoofing.
     */
    public function test_review_store_enforces_server_authoritative_order_id_preventing_spoofing(): void
    {
        $legitCustomer = Customer::create([
            'name' => 'Legit Buyer',
            'email' => 'legit@test.com',
            'phone' => '081299990002',
            'password' => 'secret123',
            'is_active' => true,
            'remember_token' => 'mlg_cust_legit_token_123',
        ]);

        $victimCustomer = Customer::create([
            'name' => 'Victim User',
            'email' => 'victim@test.com',
            'phone' => '081299990003',
            'password' => 'secret123',
            'is_active' => true,
        ]);

        $victimOrder = Order::create([
            'order_number' => 'MLG-20260910-VICTIM',
            'customer_id' => $victimCustomer->id,
            'source' => 'storefront',
            'order_status' => \App\Enums\OrderStatus::Completed,
            'payment_status' => \App\Enums\PaymentStatus::Paid,
            'subtotal' => 500000,
            'grand_total' => 500000,
        ]);

        $legitOrder = Order::create([
            'order_number' => 'MLG-20260910-LEGIT',
            'customer_id' => $legitCustomer->id,
            'source' => 'storefront',
            'order_status' => \App\Enums\OrderStatus::Completed,
            'payment_status' => \App\Enums\PaymentStatus::Paid,
            'subtotal' => 399000,
            'grand_total' => 399000,
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $legitOrder->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'product_name' => 'Test Product',
            'variant_title' => 'Default',
            'sku' => 'SKU-002',
            'unit_price' => 399000,
            'quantity' => 1,
            'subtotal' => 399000,
        ]);

        // Attacker attempts to pass victim's order_id in payload
        $response = $this->withHeader('Authorization', 'Bearer mlg_cust_legit_token_123')
            ->postJson(route('api.v1.products.reviews.store', $this->product->id), [
                'rating' => 5,
                'headline' => 'Testing Spoofing',
                'review' => 'Review content <script>alert(1)</script>',
                'order_id' => $victimOrder->id,
            ]);

        $response->assertStatus(201);

        // Verify the saved review is bound to legitOrder->id, NEVER victimOrder->id!
        $this->assertDatabaseHas('product_reviews', [
            'customer_id' => $legitCustomer->id,
            'order_id' => $legitOrder->id,
        ]);
        $this->assertDatabaseMissing('product_reviews', [
            'customer_id' => $legitCustomer->id,
            'order_id' => $victimOrder->id,
        ]);

        // Also verify XSS strip_tags
        $savedReview = \App\Models\ProductReview::where('customer_id', $legitCustomer->id)->first();
        $this->assertStringNotContainsString('<script>', $savedReview->review);
    }

    /**
     * SEC-NEW-03: Tier voucher rejects non-existent or inactive customer and CreateOrderAction binds usage to authenticated customer.
     */
    public function test_tier_voucher_and_checkout_usage_quota_integrity(): void
    {
        $vipVoucher = Voucher::create([
            'code' => 'VIPONLY50',
            'name' => 'VIP 50% Discount',
            'type' => VoucherType::Percentage,
            'amount' => 50,
            'min_order_amount' => 100000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'allow_guest' => false,
        ]);

        \App\Models\MembershipTier::create([
            'name' => 'VIP Platinum',
            'slug' => 'vip-platinum',
            'min_spend' => 1000000,
            'voucher_id' => $vipVoucher->id,
            'badge_color' => 'gold',
            'order' => 1,
            'is_active' => true,
        ]);

        $action = app(\App\Actions\Marketing\ValidateVoucherAction::class);

        // 1. Non-existent customer ID must NOT bypass tier check
        $resGhost = $action->execute('VIPONLY50', 200000, 15000, 'ghost@test.com', null, 999999);
        $this->assertFalse($resGhost['valid'], "Nonexistent customer ID TIDAK boleh membypass tier check!");

        // 2. Customer with low spend must be rejected
        $silverCustomer = Customer::create([
            'name' => 'Silver User',
            'email' => 'silver@test.com',
            'phone' => '081299990004',
            'password' => 'secret123',
            'total_spend_amount' => 50000,
            'is_active' => true,
        ]);

        $resSilver = $action->execute('VIPONLY50', 200000, 15000, $silverCustomer->email, null, $silverCustomer->id);
        $this->assertFalse($resSilver['valid'], "Customer di bawah min spend tier harus ditolak!");

        // 3. Authenticated VIP member checkout binds VoucherUsage to the member's account
        $vipCustomer = Customer::create([
            'name' => 'VIP Member',
            'email' => 'vip@test.com',
            'phone' => '081299990005',
            'password' => 'secret123',
            'total_spend_amount' => 1500000,
            'is_active' => true,
            'remember_token' => 'mlg_cust_vip_token_123',
        ]);

        $checkoutResponse = $this->withHeader('Authorization', 'Bearer mlg_cust_vip_token_123')
            ->postJson(route('api.v1.orders.checkout'), [
                'customer' => [
                    'name' => 'Burner Name',
                    'email' => 'burner@test.com',
                    'phone' => '081299990006',
                ],
                'shipping_address' => [
                    'recipient_name' => 'Burner Recipient',
                    'phone' => '081299990006',
                    'address_line1' => 'Jl. Uji No. 1',
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '10220',
                ],
                'items' => [
                    [
                        'variant_id' => $this->variant->id,
                        'quantity' => 1,
                    ],
                ],
                'voucher_code' => 'VIPONLY50',
            ]);

        $checkoutResponse->assertStatus(201);

        // Voucher usage MUST be recorded for VIP customer ID (preventing infinite reuse)
        $this->assertDatabaseHas('voucher_usages', [
            'voucher_id' => $vipVoucher->id,
            'customer_id' => $vipCustomer->id,
        ]);

        // Attempting to reuse the 1-time voucher must be rejected on next validation
        $resSecond = $action->execute('VIPONLY50', 200000, 15000, $vipCustomer->email, null, $vipCustomer->id);
        $this->assertFalse($resSecond['valid'], "Voucher 1x pakai tidak boleh digunakan kembali oleh member yang sama!");
    }

    /**
     * SEC-NEW-04: Biteship webhook with configured secret rejects requests without matching secret.
     */
    public function test_biteship_webhook_with_configured_secret_rejects_unauthenticated_request(): void
    {
        config(['biteship.webhook_secret' => 'super_secret_biteship_token_xyz']);

        $response = $this->postJson(route('api.v1.webhooks.biteship'), [
            'status' => 'delivered',
            'courier_waybill_id' => 'WAYBILL12345',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);

        // Supplying valid header secret is accepted
        $validResponse = $this->withHeader('X-Biteship-Secret', 'super_secret_biteship_token_xyz')
            ->postJson(route('api.v1.webhooks.biteship'), [
                'status' => 'delivered',
                'courier_waybill_id' => 'NONEXISTENT_WAYBILL',
            ]);

        // Secret passed, but shipment not found -> 400
        $validResponse->assertStatus(400);

        // Reset config
        config(['biteship.webhook_secret' => '']);
    }

    /**
     * SEC-NEW-06: Public order tracking masks customer PII for unauthenticated visitors.
     */
    public function test_public_order_tracking_masks_customer_pii_for_guests(): void
    {
        $order = Order::create([
            'order_number' => 'MLG-20260910-PII-TEST',
            'source' => 'storefront',
            'subtotal' => 500000,
            'grand_total' => 515000,
        ]);

        $order->address()->create([
            'recipient_name' => 'Budi Sudarsono',
            'phone' => '081234567890',
            'address_line1' => 'Jl. Sudirman No. 45 Jakarta Pusat',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10220',
        ]);

        \Livewire\Livewire::test(\App\Livewire\Public\OrderTracking::class, ['order_number' => 'MLG-20260910-PII-TEST'])
            ->assertSee('Bu*** S***')
            ->assertSee('0812****890')
            ->assertSee('Jl. Sudirm **** (Disamarkan demi privasi)')
            ->assertDontSee('081234567890')
            ->assertDontSee('Jl. Sudirman No. 45 Jakarta Pusat');
    }

    /**
     * SEC-NEW-07: syncWishlist rejects unbounded payload array.
     */
    public function test_sync_wishlist_rejects_unbounded_payload(): void
    {
        $customer = Customer::create([
            'name' => 'Wishlist User',
            'email' => 'wishlist@test.com',
            'phone' => '081299990007',
            'password' => 'secret123',
            'is_active' => true,
            'remember_token' => 'mlg_cust_wishlist_token_123',
        ]);

        // Attempt to send 105 items (max is 100)
        $giantList = array_map(fn ($i) => "prod-{$i}", range(1, 105));

        $response = $this->withHeader('Authorization', 'Bearer mlg_cust_wishlist_token_123')
            ->postJson(route('api.v1.customers.wishlist'), [
                'wishlist' => $giantList,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['wishlist']);
    }
}


