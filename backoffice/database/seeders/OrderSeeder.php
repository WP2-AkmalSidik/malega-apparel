<?php

namespace Database\Seeders;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\UpdateFulfillmentAction;
use App\Actions\Orders\UpdateOrderStatusAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Seed sample commerce orders.
     */
    public function run(
        CreateOrderAction $createOrder,
        UpdateOrderStatusAction $updateStatus,
        UpdateFulfillmentAction $updateFulfillment
    ): void {
        $admin = User::first();

        $vOxfordS = ProductVariant::where('sku', 'MLG-OXF-NVY-S')->first();
        $vOxfordM = ProductVariant::where('sku', 'MLG-OXF-NVY-M')->first();
        $vChino30 = ProductVariant::where('sku', 'MLG-CHN-KHK-30')->first();
        $vBeltStd = ProductVariant::where('sku', 'MLG-BLT-BRN-STD')->first();
        $vOvercoat = ProductVariant::where('sku', 'MLG-OVC-BLK-M')->first();

        if (! $vOxfordS || ! $vChino30) {
            return;
        }

        // 1. Order 1: Pending Unpaid Order
        $createOrder->execute([
            'customer' => [
                'name' => 'Bambang Sudarmono',
                'email' => 'bambang.sudarmono@gmail.com',
                'phone' => '081289123456',
            ],
            'items' => [
                ['variant_id' => $vOxfordM->id, 'quantity' => 1],
                ['variant_id' => $vBeltStd->id, 'quantity' => 1],
            ],
            'address' => [
                'recipient_name' => 'Bambang Sudarmono',
                'phone' => '081289123456',
                'address_line1' => 'Jl. Senopati No. 45, Kebayoran Baru',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'courier_name' => 'JNE REG',
            ],
            'shipping_total' => 20000,
            'discount_total' => 0,
            'notes' => 'Tolong bungkus rapi dengan kotak gift premium Malega.',
            'user_id' => $admin?->id,
        ]);

        // 2. Order 2: Processing & Paid Order (Shipped with Resi)
        $order2 = $createOrder->execute([
            'customer' => [
                'name' => 'Rian Hidayat',
                'email' => 'rian.hidayat@outlook.com',
                'phone' => '081399887766',
            ],
            'items' => [
                ['variant_id' => $vChino30->id, 'quantity' => 1],
            ],
            'address' => [
                'recipient_name' => 'Rian Hidayat',
                'phone' => '081399887766',
                'address_line1' => 'Cluster Florencia Blok C3/12, BSD City',
                'city' => 'Tangerang Selatan',
                'province' => 'Banten',
                'postal_code' => '15310',
                'courier_name' => 'SiCepat BEST',
            ],
            'shipping_total' => 18000,
            'discount_total' => 50000,
            'notes' => null,
            'user_id' => $admin?->id,
        ]);

        $updateStatus->execute($order2, [
            'payment_status' => PaymentStatus::Paid,
            'user_id' => $admin?->id,
        ]);

        $updateFulfillment->execute($order2, [
            'courier_name' => 'SiCepat BEST',
            'tracking_number' => '004128947291',
            'user_id' => $admin?->id,
        ]);

        // 3. Order 3: Completed Order
        $order3 = $createOrder->execute([
            'customer' => [
                'name' => 'Dimas Arya Pratama',
                'email' => 'dimas.arya@corporate.id',
                'phone' => '081122334455',
            ],
            'items' => [
                ['variant_id' => $vOvercoat->id, 'quantity' => 1],
                ['variant_id' => $vOxfordS->id, 'quantity' => 2],
            ],
            'address' => [
                'recipient_name' => 'Dimas Arya Pratama',
                'phone' => '081122334455',
                'address_line1' => 'Gedung Menara Sudirman Lt. 18',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10220',
                'courier_name' => 'JNE YES',
            ],
            'shipping_total' => 35000,
            'discount_total' => 100000,
            'notes' => 'Antar di jam kantor (09:00 - 17:00).',
            'user_id' => $admin?->id,
        ]);

        $updateStatus->execute($order3, [
            'payment_status' => PaymentStatus::Paid,
            'user_id' => $admin?->id,
        ]);

        $updateFulfillment->execute($order3, [
            'courier_name' => 'JNE YES',
            'tracking_number' => 'JNE01928472910',
            'user_id' => $admin?->id,
        ]);

        $updateStatus->execute($order3, [
            'order_status' => OrderStatus::Completed,
            'notes' => 'Paket diterima oleh resepsionis kantor',
            'user_id' => $admin?->id,
        ]);
    }
}
