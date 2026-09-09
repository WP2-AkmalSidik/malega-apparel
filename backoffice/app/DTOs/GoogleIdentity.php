<?php

namespace App\DTOs;

readonly class GoogleIdentity
{
    public function __construct(
        public string $subject,
        public string $email,
        public bool $emailVerified,
        public string $name,
        public ?string $avatarUrl = null,
    ) {}

    /**
     * Create a normalized DTO from Google token payload claims.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $subject = (string) ($payload['sub'] ?? '');
        $email = strtolower(trim((string) ($payload['email'] ?? '')));
        $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $name = trim((string) ($payload['name'] ?? ''));
        if ($name === '') {
            $name = explode('@', $email)[0] ?: 'Pelanggan Malega';
        }
        $avatarUrl = isset($payload['picture']) && filter_var($payload['picture'], FILTER_VALIDATE_URL)
            ? (string) $payload['picture']
            : null;

        return new self(
            subject: $subject,
            email: $email,
            emailVerified: $emailVerified,
            name: $name,
            avatarUrl: $avatarUrl,
        );
    }
}
