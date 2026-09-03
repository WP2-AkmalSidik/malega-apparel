<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductDetailResource;
use App\Http\Resources\V1\ProductListResource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Public active products catalog with search, filters & pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min(50, (int) $request->input('per_page', 15)));

        $query = Product::active()
            ->with(['category', 'variants.inventoryItem'])
            ->when($request->filled('category'), function ($q) use ($request) {
                $cat = $request->input('category');
                $q->whereHas('category', function ($sub) use ($cat) {
                    is_numeric($cat) ? $sub->where('id', $cat) : $sub->where('slug', $cat);
                });
            })
            ->when($request->filled('collection'), function ($q) use ($request) {
                $col = $request->input('collection');
                $q->whereHas('collections', function ($sub) use ($col) {
                    is_numeric($col) ? $sub->where('collections.id', $col) : $sub->where('collections.slug', $col);
                });
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->input('search').'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            });

        // Sorting
        match ($request->input('sort')) {
            'price_asc' => $query->orderBy(
                ProductVariant::select('price')
                    ->whereColumn('product_variants.product_id', 'products.id')
                    ->orderBy('price', 'asc')
                    ->limit(1)
            ),
            'price_desc' => $query->orderByDesc(
                ProductVariant::select('price')
                    ->whereColumn('product_variants.product_id', 'products.id')
                    ->orderBy('price', 'desc')
                    ->limit(1)
            ),
            default => $query->latest('id'),
        };

        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Katalog produk berhasil dimuat.',
            'data' => ProductListResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    /**
     * Get single active product detail by slug or ID.
     */
    public function show(string $identifier): JsonResponse
    {
        $product = Product::active()
            ->with(['category', 'collections', 'images', 'variants.inventoryItem', 'fabricSpecification'])
            ->where(function ($q) use ($identifier) {
                $q->where('slug', $identifier);
                if (is_numeric($identifier)) {
                    $q->orWhere('id', $identifier);
                }
            })
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan atau sedang tidak aktif.',
            ], 404);
        }

        $related = Product::active()
            ->with(['category', 'variants.inventoryItem'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil dimuat.',
            'data' => new ProductDetailResource($product),
            'related' => ProductListResource::collection($related),
        ]);
    }
}
