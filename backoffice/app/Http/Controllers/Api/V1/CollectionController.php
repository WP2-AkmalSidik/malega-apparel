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
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar koleksi tematik berhasil dimuat.',
            'data' => CollectionResource::collection($collections),
        ]);
    }
}
