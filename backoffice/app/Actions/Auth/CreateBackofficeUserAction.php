<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateBackofficeUserAction
{
    /**
     * Create a new backoffice staff member.
     *
     * @param  array{name: string, email: string, password: string, role?: UserRole|string, is_active?: bool}  $data
     *
     * @throws ValidationException
     */
    public function execute(array $data): User
    {
        $role = $data['role'] ?? UserRole::Admin;
        if (is_string($role)) {
            $role = UserRole::tryFrom($role) ?? throw ValidationException::withMessages([
                'role' => 'Role pengguna tidak valid.',
            ]);
        }

        return User::create([
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => $role,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
}
