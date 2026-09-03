<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Payment\HandleDuitkuCallbackAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DuitkuWebhookController extends Controller
{
    /**
     * Handle incoming Duitku payment callback / webhook notification.
     */
    public function handle(Request $request, HandleDuitkuCallbackAction $handleCallback): Response
    {
        $payload = $request->all();

        $result = $handleCallback->execute($payload);

        return response($result['message'], $result['http_status'])
            ->header('Content-Type', 'text/plain');
    }
}
