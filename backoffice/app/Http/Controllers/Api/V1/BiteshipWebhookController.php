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
        $configuredSecret = (string) config('biteship.webhook_secret');
        if (! empty($configuredSecret)) {
            $incomingSecret = $request->header('X-Biteship-Signature')
                ?: $request->header('X-Webhook-Secret')
                ?: $request->header('X-Biteship-Secret')
                ?: $request->query('secret')
                ?: $request->query('token')
                ?: ($request->bearerToken() ?? '');

            if (empty($incomingSecret) || ! hash_equals($configuredSecret, (string) $incomingSecret)) {
                \Illuminate\Support\Facades\Log::warning('Biteship Webhook: Unauthorized request - invalid or missing webhook secret.');

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid webhook secret or signature.',
                ], 401);
            }
        }

        $payload = $request->all();

        $result = $action->execute($payload);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 400);
    }
}
