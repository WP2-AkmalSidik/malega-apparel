<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class CreateCustomerAction
{
    /**
     * Create a new customer record.
     *
     * @param  array{name: string, email: string, phone: string}  $data
     *
     * @throws ValidationException
     */
    public function execute(array $data): Customer
    {
        $email = trim(strtolower($data['email']));
        $phone = trim($data['phone']);

        if (Customer::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => "Pelanggan dengan email '{$email}' sudah terdaftar.",
            ]);
        }

        return Customer::create([
            'name' => $data['name'],
            'email' => $email,
            'phone' => $phone,
            'total_orders_count' => 0,
            'total_spend_amount' => 0,
        ]);
    }
}
