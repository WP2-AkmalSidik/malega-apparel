<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    /**
     * Customer registration on Storefront.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:25',
            'password' => 'required|string|min:6',
            'marketing_opt_in' => 'nullable|boolean',
        ]);

        $existing = Customer::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ini sudah terdaftar sebagai akun anggota. Silakan login.',
                ], 422);
            }

            // If customer was created via guest checkout without password, update them
            $existing->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
                'marketing_opt_in' => $validated['marketing_opt_in'] ?? true,
                'last_login_at' => now(),
            ]);

            $customer = $existing;
        } else {
            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
                'marketing_opt_in' => $validated['marketing_opt_in'] ?? true,
                'membership_tier' => 'Silver',
                'is_active' => true,
                'last_login_at' => now(),
            ]);
        }

        $token = 'mlg_cust_'.Str::random(40);
        $customer->remember_token = $token;
        $customer->saveQuietly();

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran akun anggota Malega Apparel berhasil.',
            'data' => [
                'token' => $token,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'membership_tier' => $customer->membership_tier,
                    'marketing_opt_in' => $customer->marketing_opt_in,
                    'total_orders' => $customer->total_orders_count,
                    'total_spend' => $customer->total_spend_amount,
                    'wishlist' => $customer->wishlist ?: [],
                    'saved_addresses' => $customer->saved_addresses ?: [],
                ],
            ],
        ], 201);
    }

    /**
     * Customer login on Storefront.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $query = $validated['email_or_phone'];
        $customer = Customer::where('email', $query)
            ->orWhere('phone', $query)
            ->first();

        if (! $customer || ! $customer->password || ! Hash::check($validated['password'], $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email/Nomor WhatsApp atau kata sandi tidak cocok.',
            ], 401);
        }

        if (! $customer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun dinonaktifkan. Silakan hubungi Customer Service.',
            ], 403);
        }

        $token = 'mlg_cust_'.Str::random(40);
        $customer->last_login_at = now();
        $customer->remember_token = $token;
        $customer->saveQuietly();

        return response()->json([
            'success' => true,
            'message' => 'Selamat datang kembali di Malega Apparel, '.$customer->name.'!',
            'data' => [
                'token' => $token,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'membership_tier' => $customer->membership_tier,
                    'marketing_opt_in' => $customer->marketing_opt_in,
                    'total_orders' => $customer->total_orders_count,
                    'total_spend' => $customer->total_spend_amount,
                    'wishlist' => $customer->wishlist ?: [],
                    'saved_addresses' => $customer->saved_addresses ?: [],
                ],
            ],
        ]);
    }

    /**
     * Get current authenticated customer details.
     */
    public function me(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomerFromToken($request);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid atau telah berakhir.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'membership_tier' => $customer->membership_tier,
                'marketing_opt_in' => $customer->marketing_opt_in,
                'total_orders' => $customer->total_orders_count,
                'total_spend' => $customer->total_spend_amount,
                'formatted_spend' => $customer->formatted_total_spend,
                'wishlist' => $customer->wishlist ?: [],
                'saved_addresses' => $customer->saved_addresses ?: [],
            ],
        ]);
    }

    /**
     * Update customer profile & marketing preference.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomerFromToken($request);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid.',
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:25',
            'marketing_opt_in' => 'nullable|boolean',
            'saved_addresses' => 'nullable|array',
        ]);

        $customer->update(array_filter([
            'name' => $validated['name'] ?? $customer->name,
            'phone' => $validated['phone'] ?? $customer->phone,
            'marketing_opt_in' => isset($validated['marketing_opt_in']) ? (bool) $validated['marketing_opt_in'] : $customer->marketing_opt_in,
            'saved_addresses' => $validated['saved_addresses'] ?? $customer->saved_addresses,
        ], fn ($val) => ! is_null($val)));

        return response()->json([
            'success' => true,
            'message' => 'Profil dan preferensi berhasil diperbarui.',
            'data' => $customer,
        ]);
    }

    /**
     * Sync customer wishlist.
     */
    public function syncWishlist(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomerFromToken($request);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid.',
            ], 401);
        }

        $validated = $request->validate([
            'wishlist' => 'required|array',
        ]);

        $customer->update([
            'wishlist' => array_values(array_unique($validated['wishlist'])),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wishlist berhasil disinkronkan.',
            'data' => [
                'wishlist' => $customer->wishlist,
            ],
        ]);
    }

    /**
     * List customer past orders with items & shipment tracking.
     */
    public function orders(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomerFromToken($request);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid.',
            ], 401);
        }

        $orders = Order::where('customer_id', $customer->id)
            ->orWhere('customer_email', $customer->email)
            ->with(['items', 'payment', 'shipment'])
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status->value,
                    'status_label' => $order->status->label(),
                    'total_amount' => (int) $order->total_amount,
                    'formatted_total' => 'Rp '.number_format($order->total_amount, 0, ',', '.'),
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'items' => $order->items->map(fn ($item) => [
                        'title' => $item->variant_title,
                        'sku' => $item->sku,
                        'price' => (int) $item->unit_price,
                        'quantity' => (int) $item->quantity,
                        'subtotal' => (int) $item->subtotal,
                    ]),
                    'shipping' => $order->shipment ? [
                        'courier' => $order->shipment->courier_name,
                        'waybill' => $order->shipment->waybill_number,
                        'tracking_url' => url('/track?order=' . $order->order_number),
                    ] : null,
                    'payment' => $order->payment ? [
                        'method' => $order->payment->payment_method,
                        'status' => $order->payment->status,
                    ] : null,
                ];
            }),
        ]);
    }

    /**
     * Helper to resolve customer from Bearer token or header.
     */
    protected function resolveCustomerFromToken(Request $request): ?Customer
    {
        $header = $request->header('Authorization');
        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = substr($header, 7);
        if (empty($token)) {
            return null;
        }

        return Customer::where('remember_token', $token)->first();
    }
}
