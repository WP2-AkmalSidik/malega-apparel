<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\AuthenticateGoogleCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\GoogleAuthRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Services\Auth\GoogleIdentityVerifierInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

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
            return response()->json([
                'success' => false,
                'message' => 'Email ini sudah terdaftar dalam sistem. Silakan login ke akun Anda atau gunakan email lain.',
            ], 422);
        }

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
            'wishlist' => ['required', 'array', 'max:100'],
            'wishlist.*' => ['string', 'max:100'],
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

        $orders = $customer->orders()
            ->with(['items', 'payment', 'shipment'])
            ->get();
        $reviewedProductIds = \App\Models\ProductReview::where('customer_id', $customer->id)
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $orders->map(function ($order) use ($reviewedProductIds) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->order_status->value,
                    'status_label' => $order->order_status->label(),
                    'total_amount' => (int) $order->grand_total,
                    'formatted_total' => $order->formatted_grand_total,
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'items' => $order->items->map(fn ($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'title' => $item->variant_title,
                        'sku' => $item->sku,
                        'price' => (int) $item->unit_price,
                        'quantity' => (int) $item->quantity,
                        'subtotal' => (int) $item->subtotal,
                        'has_reviewed' => in_array($item->product_id, $reviewedProductIds),
                    ]),
                    'shipping' => $order->shipment ? [
                        'courier' => $order->shipment->courier_company ?: $order->shipment->courier_service_name ?: 'Kurir Rekanan',
                        'waybill' => $order->shipment->waybill_id ?: '-',
                        'tracking_url' => url('/track?order='.$order->order_number),
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
     * Authenticate customer with verified Google ID token.
     */
    public function google(
        GoogleAuthRequest $request,
        GoogleIdentityVerifierInterface $verifier,
        AuthenticateGoogleCustomerAction $action
    ): JsonResponse {
        try {
            $credential = $request->validated('credential');
            $identity = $verifier->verify($credential);
            $result = $action->execute($identity, $request);

            /** @var Customer $customer */
            $customer = $result['customer'];
            $token = $result['token'];

            return response()->json([
                'success' => true,
                'message' => 'Selamat datang di Malega Apparel, '.$customer->name.'!',
                'data' => [
                    'token' => $token,
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'avatar' => $customer->avatar,
                        'membership_tier' => $customer->membership_tier,
                        'marketing_opt_in' => $customer->marketing_opt_in,
                        'total_orders' => $customer->total_orders_count,
                        'total_spend' => $customer->total_spend_amount,
                        'wishlist' => $customer->wishlist ?: [],
                        'saved_addresses' => $customer->saved_addresses ?: [],
                    ],
                ],
            ]);
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Autentikasi Google gagal.',
            ], 401);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Akses ditolak.',
            ], 403);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('Google Auth: Terjadi kesalahan internal saat login Google.', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses login Google. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }

    /**
     * Customer logout from Storefront session and token invalidation.
     */
    public function logout(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomerFromToken($request);

        if ($customer) {
            $customer->remember_token = null;
            $customer->saveQuietly();
        }

        Auth::guard('customer')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'success' => true,
            'message' => 'Anda telah berhasil keluar dari akun Malega Apparel.',
        ]);
    }

    /**
     * Helper to resolve customer from stateful session, Sanctum guard, or Bearer token.
     */
    protected function resolveCustomerFromToken(Request $request): ?Customer
    {
        $customer = null;

        // 1. First-party Laravel Sanctum session auth on customer guard
        $sessionCustomer = Auth::guard('customer')->user();
        if ($sessionCustomer instanceof Customer) {
            $customer = $sessionCustomer;
        }

        // 2. Sanctum guard request user check
        if (! $customer) {
            $sanctumCustomer = $request->user('customer');
            if ($sanctumCustomer instanceof Customer) {
                $customer = $sanctumCustomer;
            }
        }

        // 3. Fallback: Bearer remember_token in Authorization header
        if (! $customer) {
            $header = $request->header('Authorization');
            if ($header && str_starts_with($header, 'Bearer ')) {
                $token = trim(substr($header, 7));
                if (! empty($token)) {
                    $customer = Customer::where('remember_token', $token)->first();
                }
            }
        }

        if (! $customer || ! $customer->is_active) {
            return null;
        }

        return $customer;
    }
}
