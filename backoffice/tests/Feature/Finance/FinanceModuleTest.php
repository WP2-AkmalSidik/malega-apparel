<?php

namespace Tests\Feature\Finance;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create([
            'email' => 'finance@malega.id',
            'name' => 'Finance Staff',
        ]);
    }

    protected function createOrderWithPayment(string $method = 'BC', int $amount = 250000, string $status = 'success'): array
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
        ]);

        $order = Order::create([
            'order_number' => 'MLG-' . uniqid(),
            'customer_id' => $customer->id,
            'source' => 'web',
            'order_status' => $status === 'success' ? OrderStatus::Processing : OrderStatus::Pending,
            'payment_status' => $status === 'success' ? PaymentStatus::Paid : PaymentStatus::Unpaid,
            'subtotal' => $amount,
            'shipping_total' => 15000,
            'grand_total' => $amount + 15000,
        ]);

        $adminFee = Payment::estimateGatewayFee($method, $order->grand_total);
        $netAmount = $order->grand_total - $adminFee;

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_gateway' => 'duitku',
            'merchant_order_id' => $order->order_number,
            'reference' => 'DS34926' . uniqid(),
            'payment_method' => $method,
            'payment_method_name' => $method === 'BC' ? 'BCA Virtual Account' : 'QRIS',
            'amount' => $order->grand_total,
            'admin_fee' => $adminFee,
            'net_amount' => $netAmount,
            'status' => $status,
            'paid_at' => $status === 'success' ? now() : null,
        ]);

        return [$order, $payment];
    }

    public function test_authenticated_staff_can_view_payment_logs_page(): void
    {
        $this->createOrderWithPayment('BC', 300000, 'success');
        $this->createOrderWithPayment('SP', 200000, 'pending');

        $response = $this->actingAs($this->staff)->get(route('finance.payment-logs'));
        $response->assertOk()
            ->assertSeeText('Logs Pembayaran')
            ->assertSeeText('BCA Virtual Account')
            ->assertSeeText('LUNAS (PAID)');
    }

    public function test_authenticated_staff_can_view_cash_flow_page(): void
    {
        $this->createOrderWithPayment('BC', 500000, 'success');

        $response = $this->actingAs($this->staff)->get(route('finance.cash-flow'));
        $response->assertOk()
            ->assertSeeText('Arus Kas & Pendapatan Bersih')
            ->assertSeeText('KAS BERSIH DITERIMA (NET)');
    }

    public function test_authenticated_staff_can_view_financial_reports_page(): void
    {
        $this->createOrderWithPayment('BC', 400000, 'success');
        $this->createOrderWithPayment('SP', 250000, 'success');

        $response = $this->actingAs($this->staff)->get(route('finance.reports'));
        $response->assertOk()
            ->assertSeeText('Laporan Keuangan & Laba Bersih')
            ->assertSeeText('Ringkasan Laporan Pendapatan');
    }

    public function test_payment_model_calculates_admin_fee_and_net_amount_accurately(): void
    {
        // VA BCA flat fee Rp 4.000
        $feeVa = Payment::estimateGatewayFee('BC', 265000);
        $this->assertEquals(4000, $feeVa);

        // QRIS (0.7% MDR) on Rp 200.000 = Rp 1.400
        $feeQris = Payment::estimateGatewayFee('QR', 200000);
        $this->assertEquals(1400, $feeQris);

        // Credit Card (2.0% + 2000) on Rp 500.000 = Rp 12.000
        $feeCc = Payment::estimateGatewayFee('VC', 500000);
        $this->assertEquals(12000, $feeCc);
    }

    public function test_payment_logs_livewire_can_filter_by_status(): void
    {
        [$order1, $p1] = $this->createOrderWithPayment('BC', 300000, 'success');
        [$order2, $p2] = $this->createOrderWithPayment('SP', 200000, 'pending');

        Livewire::actingAs($this->staff, 'web')
            ->test(\App\Livewire\Finance\PaymentLogsIndex::class)
            ->assertSee($order1->order_number)
            ->assertSee($order2->order_number)
            ->set('statusFilter', 'pending')
            ->assertSee($order2->order_number)
            ->assertDontSee($order1->order_number);
    }

    public function test_cash_flow_livewire_computes_real_net_revenue_correctly(): void
    {
        // 1. Success payment: Gross 265.000, Fee 4.000, Net 261.000
        $this->createOrderWithPayment('BC', 250000, 'success');

        Livewire::actingAs($this->staff, 'web')
            ->test(\App\Livewire\Finance\CashFlowIndex::class)
            ->assertSee('265.000')
            ->assertSee('4.000')
            ->assertSee('261.000');
    }

    public function test_financial_report_livewire_groups_channel_breakdown(): void
    {
        $this->createOrderWithPayment('BC', 250000, 'success');
        $this->createOrderWithPayment('SP', 100000, 'success');

        Livewire::actingAs($this->staff, 'web')
            ->test(\App\Livewire\Finance\FinancialReportIndex::class)
            ->assertSee('BCA Virtual Account')
            ->assertSee('QRIS');
    }
}
