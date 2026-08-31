<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Logistics\HandleBiteshipWebhookAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiteshipWebhookController extends Controller
{
    /**
     * Handle incoming Biteship webhook event.
     */
    public function handle(Request $request, HandleBiteshipWebhookAction $action): JsonResponse
    {
        $payload = $request->all();

        $result = $action->execute($payload);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 400);
    }
}
