<?php

namespace App\Services\Logistics;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected string $apiKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('biteship.api_key');
        $this->baseUrl = rtrim((string) config('biteship.base_url', 'https://api.biteship.com/v1'), '/');
    }

    /**
     * Get list of supported couriers from Biteship.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCouriers(): array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/couriers");

            if ($response->successful()) {
                return $response->json('couriers', []);
            }

            Log::error('Biteship getCouriers failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (Exception $e) {
            Log::error('Biteship getCouriers exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check rates from multiple couriers.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function checkRates(array $payload): array
    {
        $response = $this->client()->post("{$this->baseUrl}/rates/couriers", $payload);

        return $this->handleResponse($response, 'checkRates');
    }

    /**
     * Create an order in Biteship to generate Auto-AWB.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function createOrder(array $payload): array
    {
        $response = $this->client()->post("{$this->baseUrl}/orders", $payload);

        return $this->handleResponse($response, 'createOrder');
    }

    /**
     * Get order details by Biteship Order ID.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function getOrder(string $biteshipOrderId): array
    {
        $response = $this->client()->get("{$this->baseUrl}/orders/{$biteshipOrderId}");

        return $this->handleResponse($response, 'getOrder');
    }

    /**
     * Get real-time tracking milestones by Tracking ID.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function getTracking(string $trackingId): array
    {
        $response = $this->client()->get("{$this->baseUrl}/trackings/{$trackingId}");

        return $this->handleResponse($response, 'getTracking');
    }

    /**
     * Cancel an existing order on Biteship.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function cancelOrder(string $biteshipOrderId, string $reason = 'Customer cancellation'): array
    {
        $response = $this->client()->post("{$this->baseUrl}/orders/{$biteshipOrderId}/cancel", [
            'reason' => $reason,
        ]);

        return $this->handleResponse($response, 'cancelOrder');
    }

    /**
     * Get base HTTP client with authorization header.
     */
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(15);
    }

    /**
     * Process response and throw descriptive exception on failure.
     *
     * @throws Exception
     */
    protected function handleResponse(Response $response, string $context): array
    {
        $data = $response->json();

        if ($response->successful() && ($data['success'] ?? true)) {
            return $data;
        }

        $errorMessage = $data['error'] ?? $data['message'] ?? 'Unknown error from Biteship API';

        Log::error("Biteship {$context} error", [
            'status' => $response->status(),
            'response' => $data,
        ]);

        throw new Exception("Biteship [{$context}]: {$errorMessage}");
    }
}
