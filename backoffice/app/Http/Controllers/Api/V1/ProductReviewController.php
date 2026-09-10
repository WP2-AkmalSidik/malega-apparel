<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Get reviews and rating summary for a product.
     */
    public function index(Request $request, string $productId): JsonResponse
    {
        $product = Product::where('id', $productId)
            ->orWhere('slug', $productId)
            ->firstOrFail();

        $approvedReviews = ProductReview::where('product_id', $product->id)
            ->approved()
            ->with('customer:id,name,membership_tier,avatar')
            ->recent()
            ->get();

        $totalReviews = $approvedReviews->count();
        $averageRating = $totalReviews > 0
            ? round((float) $approvedReviews->avg('rating'), 1)
            : 0.0;

        // Breakdown distribution
        $starCounts = [
            5 => $approvedReviews->where('rating', 5)->count(),
            4 => $approvedReviews->where('rating', 4)->count(),
            3 => $approvedReviews->where('rating', 3)->count(),
            2 => $approvedReviews->where('rating', 2)->count(),
            1 => $approvedReviews->where('rating', 1)->count(),
        ];

        $starPercentages = [];
        foreach ($starCounts as $star => $count) {
            $starPercentages[$star] = $totalReviews > 0
                ? (int) round(($count / $totalReviews) * 100)
                : 0;
        }

        // Check if caller is authenticated customer & eligible to review
        $canReview = false;
        $hasReviewed = false;
        $eligibleOrderId = null;

        $customer = $this->resolveCustomer($request);
        if ($customer && $customer->is_active) {
            $hasReviewed = ProductReview::where('product_id', $product->id)
                ->where('customer_id', $customer->id)
                ->exists();

            $orderItem = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($q) use ($customer) {
                    $q->where('customer_id', $customer->id)
                        ->where('payment_status', 'paid')
                        ->whereNotIn('order_status', ['cancelled']);
                })
                ->latest()
                ->first();

            if ($orderItem && ! $hasReviewed) {
                $canReview = true;
                $eligibleOrderId = $orderItem->order_id;
            }
        }

        $formattedReviews = $approvedReviews->map(function (ProductReview $r) {
            // Mask customer name for privacy (e.g. "Budi S.")
            $nameParts = explode(' ', $r->customer?->name ?? 'Pelanggan');
            $maskedName = count($nameParts) > 1
                ? $nameParts[0] . ' ' . strtoupper(substr($nameParts[1], 0, 1)) . '.'
                : $nameParts[0];

            return [
                'id' => $r->id,
                'customer_name' => $maskedName,
                'customer_tier' => $r->customer?->membership_tier ?? 'Silver',
                'customer_avatar' => $r->customer?->avatar,
                'rating' => $r->rating,
                'headline' => $r->headline,
                'review' => $r->review,
                'fit_rating' => $r->fit_rating,
                'fit_label' => $r->fitLabel(),
                'is_verified_purchase' => $r->is_verified_purchase,
                'admin_reply' => $r->admin_reply,
                'admin_replied_at' => $r->admin_replied_at?->format('d M Y'),
                'created_at' => $r->created_at->format('d M Y'),
                'created_at_human' => $r->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'summary' => [
                    'average_rating' => $averageRating,
                    'total_reviews' => $totalReviews,
                    'star_counts' => $starCounts,
                    'star_percentages' => $starPercentages,
                ],
                'reviews' => $formattedReviews,
                'user_eligibility' => [
                    'can_review' => $canReview,
                    'has_reviewed' => $hasReviewed,
                    'eligible_order_id' => $eligibleOrderId,
                ],
            ],
        ]);
    }

    /**
     * Helper to resolve customer from Bearer token.
     */
    protected function resolveCustomer(Request $request): ?Customer
    {
        $authHeader = $request->header('Authorization');
        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($authHeader, 7));
        if (empty($token)) {
            return null;
        }

        return Customer::where('remember_token', $token)->first();
    }

    /**
     * Store a new product review (Verified Buyer Only).
     */
    public function store(Request $request, string $productId): JsonResponse
    {
        $product = Product::where('id', $productId)
            ->orWhere('slug', $productId)
            ->firstOrFail();

        // 1. Must be authenticated active member
        $authHeader = $request->header('Authorization');
        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya member terdaftar yang dapat memberikan rating & ulasan. Silakan masuk ke akun Anda.',
            ], 401);
        }

        $customer = $this->resolveCustomer($request);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi akun tidak valid atau telah berakhir. Silakan login kembali.',
            ], 401);
        }

        if (! $customer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun dinonaktifkan. Silakan hubungi Customer Service.',
            ], 403);
        }

        // 2. Strict Verified Buyer check (Must be paid and not cancelled)
        $orderItem = OrderItem::where('product_id', $product->id)
            ->whereHas('order', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)
                    ->where('payment_status', 'paid')
                    ->whereNotIn('order_status', ['cancelled']);
            })
            ->latest()
            ->first();

        if (! $orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan hanya dapat diberikan oleh pembeli terverifikasi yang telah menyelesaikan pesanan untuk produk ini.',
            ], 403);
        }

        // 3. Anti-spam check (one review per product per customer)
        $alreadyReviewed = ProductReview::where('product_id', $product->id)
            ->where('customer_id', $customer->id)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah memberikan ulasan untuk produk ini.',
            ], 422);
        }

        // 4. Validate review content (order_id is strictly server-authoritative from verified orderItem)
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'headline' => ['nullable', 'string', 'max:150'],
            'review' => ['required', 'string', 'min:5', 'max:1000'],
            'fit_rating' => ['nullable', 'string', 'in:true_to_size,runs_small,runs_large'],
        ]);

        $headline = ! empty($validated['headline']) ? strip_tags(trim($validated['headline'])) : null;
        $reviewText = strip_tags(trim($validated['review']));

        $review = ProductReview::create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'order_id' => $orderItem->order_id,
            'order_item_id' => $orderItem->id,
            'rating' => (int) $validated['rating'],
            'headline' => $headline,
            'review' => $reviewText,
            'fit_rating' => $validated['fit_rating'] ?? 'true_to_size',
            'is_verified_purchase' => true,
            'status' => 'approved',
        ]);

        // Product rating is automatically updated via ProductReview model boot

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Ulasan dan rating Anda berhasil dipublikasikan sebagai Pembeli Terverifikasi.',
            'data' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'headline' => $review->headline,
                'review' => $review->review,
                'fit_label' => $review->fitLabel(),
                'product_rating' => $product->fresh()->rating,
                'product_review_count' => $product->fresh()->review_count,
            ],
        ], 201);
    }

    /**
     * Get list of reviews submitted by current customer.
     */
    public function customerReviews(Request $request): JsonResponse
    {
        $customer = $this->resolveCustomer($request);

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (! $customer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun dinonaktifkan.',
            ], 403);
        }

        $reviews = ProductReview::where('customer_id', $customer->id)
            ->with('product:id,name,slug,featured_image')
            ->recent()
            ->get()
            ->map(fn (ProductReview $r) => [
                'id' => $r->id,
                'product_id' => $r->product_id,
                'product_name' => $r->product?->name,
                'product_slug' => $r->product?->slug,
                'product_image' => $r->product?->featured_image,
                'rating' => $r->rating,
                'headline' => $r->headline,
                'review' => $r->review,
                'fit_label' => $r->fitLabel(),
                'status' => $r->status,
                'admin_reply' => $r->admin_reply,
                'created_at' => $r->created_at->format('d M Y'),
            ]);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }
}
