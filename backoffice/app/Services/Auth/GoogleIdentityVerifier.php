<?php

namespace App\Services\Auth;

use App\DTOs\GoogleIdentity;
use Google_Auth_LoginTicket;
use Google_Client;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleIdentityVerifier implements GoogleIdentityVerifierInterface
{
    public function __construct(
        protected ?string $clientId = null,
        protected ?Google_Client $client = null,
    ) {
        $this->clientId = $clientId ?: (string) config('services.google.client_id');
        if (! $this->client && class_exists('Google_Client')) {
            $this->client = new Google_Client();
            if ($this->clientId !== '') {
                $this->client->setClientId($this->clientId);
            }
        }
    }

    /**
     * Verify Google ID token and return normalized identity claims.
     *
     * @throws AuthenticationException
     */
    public function verify(string $idToken): GoogleIdentity
    {
        $idToken = trim($idToken);
        if ($idToken === '') {
            throw new AuthenticationException('Token kredensial Google tidak boleh kosong.');
        }

        if (empty($this->clientId)) {
            Log::error('Google Auth: GOOGLE_CLIENT_ID belum dikonfigurasi di environment server.');
            throw new AuthenticationException('Konfigurasi Google Authentication server belum lengkap.');
        }

        $payload = null;

        // Strategy 1: Verify using google/apiclient Google_Client
        if ($this->client) {
            try {
                $result = $this->client->verifyIdToken($idToken);
                if ($result instanceof Google_Auth_LoginTicket) {
                    $attributes = $result->getAttributes();
                    $payload = $attributes['payload'] ?? null;
                } elseif (is_array($result)) {
                    $payload = $result;
                }
            } catch (Throwable $e) {
                Log::info('Google Auth: Google_Client local verification returned exception, falling back to tokeninfo.', [
                    'reason' => $e->getMessage(),
                ]);
            }
        }

        // Strategy 2: Official Google tokeninfo verification endpoint (fallback for modern GIS tokens)
        if (! is_array($payload) || empty($payload)) {
            try {
                $response = Http::timeout(7)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

                if ($response->successful()) {
                    $payload = $response->json();
                } else {
                    Log::warning('Google Auth: Google tokeninfo verification failed.', [
                        'status' => $response->status(),
                        'response' => $response->json() ?? $response->body(),
                    ]);
                }
            } catch (Throwable $e) {
                Log::error('Google Auth: Failed to communicate with Google tokeninfo endpoint.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (! is_array($payload) || empty($payload)) {
            throw new AuthenticationException('Kredensial autentikasi Google tidak valid.');
        }

        // 1. Audience validation
        $aud = (string) ($payload['aud'] ?? '');
        if ($aud !== $this->clientId) {
            Log::warning('Google Auth: Audience mismatch.', [
                'expected_aud_prefix' => substr($this->clientId, 0, 10).'...',
                'actual_aud_prefix' => substr($aud, 0, 10).'...',
            ]);
            throw new AuthenticationException('Audiens token Google tidak cocok.');
        }

        // 2. Issuer validation
        $iss = (string) ($payload['iss'] ?? '');
        if (! in_array($iss, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            Log::warning('Google Auth: Invalid issuer claim.', ['iss' => $iss]);
            throw new AuthenticationException('Penerbit token Google tidak sah.');
        }

        // 3. Expiration validation
        $exp = (int) ($payload['exp'] ?? 0);
        if ($exp <= time()) {
            throw new AuthenticationException('Token kredensial Google telah kadaluarsa. Silakan coba masuk kembali.');
        }

        // 4. Subject validation (unique Google ID)
        $sub = (string) ($payload['sub'] ?? '');
        if ($sub === '') {
            throw new AuthenticationException('Klaim subjek Google tidak ditemukan.');
        }

        // 5. Email validation
        $email = (string) ($payload['email'] ?? '');
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthenticationException('Email akun Google tidak valid.');
        }

        $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (! $emailVerified) {
            throw new AuthenticationException('Email Google belum diverifikasi oleh Google. Mohon verifikasi email akun Google Anda terlebih dahulu.');
        }

        return GoogleIdentity::fromPayload($payload);
    }
}
