<?php

namespace App\Services\Auth;

use App\DTOs\GoogleIdentity;

interface GoogleIdentityVerifierInterface
{
    /**
     * Verify Google ID token and return normalized identity claims.
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function verify(string $idToken): GoogleIdentity;
}
