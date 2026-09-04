<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Logistics\BiteshipService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * Get available shipping couriers and dynamic live rates from Biteship.
     */
    public function rates(Request $request, BiteshipService $biteshipService): JsonResponse
    {
        $validated = $request->validate([
            'destination_postal_code' => ['nullable', 'string', 'max:20'],
            'destination_city' => ['nullable', 'string', 'max:100'],
            'couriers' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.weight' => ['nullable', 'integer'],
            'items.*.quantity' => ['nullable', 'integer'],
        ]);

        $originPostalCode = (int) config('biteship.origin.postal_code', 12430);
        $destPostalCode = ! empty($validated['destination_postal_code'])
            ? (int) preg_replace('/\D/', '', $validated['destination_postal_code'])
            : 12730;

        if ($destPostalCode <= 0) {
            $destPostalCode = 12730;
        }

        // Calculate total weight (default ~350 grams per apparel piece)
        $totalWeight = 0;
        $itemsPayload = [];
        if (! empty($validated['items']) && is_array($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $weight = max(250, (int) ($item['weight'] ?? 350));
                $totalWeight += ($weight * $qty);

                $itemsPayload[] = [
                    'name' => 'Malega Apparel Piece',
                    'description' => 'Luxury Apparel',
                    'value' => 150000,
                    'length' => 30,
                    'width' => 25,
                    'height' => 2,
                    'weight' => $weight,
                    'quantity' => $qty,
                ];
            }
        }

        if (empty($itemsPayload)) {
            $itemsPayload[] = [
                'name' => 'Malega Apparel Package',
                'description' => 'Apparel Package',
                'value' => 189000,
                'length' => 30,
                'width' => 25,
                'height' => 3,
                'weight' => max(350, $totalWeight),
                'quantity' => 1,
            ];
        }

        $courierList = $validated['couriers'] ?? 'jne,sicepat,jnt,anteraja,gojek,grab';

        $biteshipPayload = [
            'origin_postal_code' => $originPostalCode,
            'destination_postal_code' => $destPostalCode,
            'couriers' => $courierList,
            'items' => $itemsPayload,
        ];

        try {
            $response = $biteshipService->checkRates($biteshipPayload);
            $pricing = $response['pricing'] ?? [];

            if (! empty($pricing) && is_array($pricing)) {
                $formattedRates = [];
                foreach ($pricing as $rate) {
                    $courierName = $rate['courier_name'] ?? $rate['courier_service_name'] ?? 'Kurir';
                    $courierCode = strtolower((string) ($rate['courier_code'] ?? 'courier'));
                    $serviceCode = strtolower((string) ($rate['courier_service_code'] ?? 'std'));
                    $serviceName = $rate['courier_service_name'] ?? strtoupper($serviceCode);
                    $price = (int) ($rate['price'] ?? 15000);
                    $etd = $rate['shipment_duration_range'] ?? $rate['duration'] ?? '1-2 Hari Kerja';

                    $tier = match ($serviceCode) {
                        'instant', 'same_day', 'sameday' => 'instant',
                        'next_day', 'nextday', 'best', 'yes' => 'priority',
                        default => 'standard'
                    };

                    $formattedRates[] = [
                        'id' => "biteship-{$courierCode}-{$serviceCode}",
                        'courier' => $courierName,
                        'courier_code' => $courierCode,
                        'service' => $serviceName,
                        'service_code' => $serviceCode,
                        'name' => "{$courierName} ({$serviceName})",
                        'cost' => $price,
                        'formatted_cost' => 'Rp ' . number_format($price, 0, ',', '.'),
                        'etd' => "Estimasi tiba: {$etd}",
                        'type' => $rate['type'] ?? 'drop_off',
                        'tier' => $tier,
                    ];
                }

                if (! empty($formattedRates)) {
                    usort($formattedRates, fn ($a, $b) => $a['cost'] <=> $b['cost']);

                    return response()->json([
                        'success' => true,
                        'source' => 'biteship_live',
                        'data' => $formattedRates,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::warning('Biteship rates API failed or returned empty: ' . $e->getMessage());
        }

        // Curated fallback rates
        $fallbackRates = [
            [
                'id' => 'spx-standard',
                'courier' => 'SPX Express',
                'courier_code' => 'spx',
                'service' => 'Standard Delivery',
                'service_code' => 'standard',
                'name' => 'SPX Express Standard',
                'cost' => 15000,
                'formatted_cost' => 'Rp 15.000',
                'etd' => 'Estimasi tiba: 1-2 Hari Kerja',
                'tier' => 'standard',
            ],
            [
                'id' => 'jnt-priority',
                'courier' => 'J&T Express',
                'courier_code' => 'jnt',
                'service' => 'Super Priority',
                'service_code' => 'priority',
                'name' => 'J&T Super Priority',
                'cost' => 18000,
                'formatted_cost' => 'Rp 18.000',
                'etd' => 'Estimasi tiba: Besok Tiba (24 Jam)',
                'tier' => 'priority',
            ],
            [
                'id' => 'sicepat-best',
                'courier' => 'SiCepat Ekspres',
                'courier_code' => 'sicepat',
                'service' => 'BEST Express',
                'service_code' => 'best',
                'name' => 'SiCepat BEST',
                'cost' => 16000,
                'formatted_cost' => 'Rp 16.000',
                'etd' => 'Estimasi tiba: 1-2 Hari',
                'tier' => 'priority',
            ],
            [
                'id' => 'instant-sameday',
                'courier' => 'GoSend / GrabExpress',
                'courier_code' => 'gojek',
                'service' => 'Instant Sameday',
                'service_code' => 'instant',
                'name' => 'Instant Sameday (Grab / Gojek)',
                'cost' => 32000,
                'formatted_cost' => 'Rp 32.000',
                'etd' => 'Estimasi tiba: Hari Ini (3 Jam Tiba)',
                'tier' => 'instant',
            ],
        ];

        return response()->json([
            'success' => true,
            'source' => 'fallback',
            'data' => $fallbackRates,
        ]);
    }
}
