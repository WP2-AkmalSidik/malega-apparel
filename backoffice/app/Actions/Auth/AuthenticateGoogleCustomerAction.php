<?php

namespace App\Actions\Auth;

use App\DTOs\GoogleIdentity;
use App\Models\Customer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthenticateGoogleCustomerAction
{
    /**
     * Authenticate or register a customer using normalized Google identity claims.
     *
     * @return array{token: string, customer: Customer}
     *
     * @throws AuthorizationException
     */
    public function execute(GoogleIdentity $identity, Request $request): array
    {
        /** @var Customer $customer */
        $customer = DB::transaction(function () use ($identity) {
            // Case A: Identity already linked via Google Subject ID
            $existingByGoogleId = Customer::where('google_id', $identity->subject)->first();
            if ($existingByGoogleId) {
                return $existingByGoogleId;
            }

            // Case B: Existing customer registered with the same verified email address
            $existingByEmail = Customer::where('email', $identity->email)->first();
            if ($existingByEmail) {
                $existingByEmail->google_id = $identity->subject;
                if (! $existingByEmail->email_verified_at) {
                    $existingByEmail->email_verified_at = now();
                }
                if (! $existingByEmail->avatar && $identity->avatarUrl) {
                    $existingByEmail->avatar = $identity->avatarUrl;
                }
                $existingByEmail->save();

                return $existingByEmail;
            }

            // Case C: Brand new customer
            return Customer::create([
                'name' => $identity->name,
                'email' => $identity->email,
                'google_id' => $identity->subject,
                'avatar' => $identity->avatarUrl,
                'email_verified_at' => now(),
                'marketing_opt_in' => true,
                'membership_tier' => 'Silver',
                'is_active' => true,
            ]);
        });

        // Check account suspension / active status
        if (! $customer->is_active) {
            throw new AuthorizationException('Akun dinonaktifkan. Silakan hubungi Customer Service.');
        }

        // Generate remember_token for bearer token compatibility
        $token = 'mlg_cust_'.Str::random(40);
        $customer->last_login_at = now();
        $customer->remember_token = $token;
        $customer->saveQuietly();

        // Establish Laravel stateful session on customer guard
        Auth::guard('customer')->login($customer);

        // Regenerate session ID to prevent session fixation attacks
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return [
            'token' => $token,
            'customer' => $customer,
        ];
    }
}
