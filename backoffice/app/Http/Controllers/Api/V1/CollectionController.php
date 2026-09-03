<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CollectionResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * List all active collections.
     */
    public function index(Request $request): JsonResponse
    {
        $collections = Collection::active()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar koleksi tematik berhasil dimuat.',
            'data' => CollectionResource::collection($collections),
        ]);
    }

    /**
     * Get single collection lookbook detail with eager-loaded active products (Zero N+1).
     */
    public function show(string $identifier): JsonResponse
    {
        $collection = Collection::active()
            ->with([
                'products' => function ($q) {
                    $q->active()->with([
                        'category:id,name,slug',
                        'fabricSpecification:id,name,brand,gramasi,material,fit_cutting,collar_hood,care_instructions',
                        'variants.inventoryItem:id,variant_id,on_hand,reserved',
                    ]);
                },
            ])
            ->where(function ($q) use ($identifier) {
                $q->where('slug', $identifier);
                if (is_numeric($identifier)) {
                    $q->orWhere('id', $identifier);
                }
            })
            ->first();

        if (! $collection) {
            return response()->json([
                'success' => false,
                'message' => 'Koleksi tidak ditemukan atau sedang tidak aktif.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail koleksi lookbook berhasil dimuat.',
            'data' => new CollectionResource($collection),
        ]);
    }
}
