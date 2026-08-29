<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Validation\ValidationException;

class UpdateCustomerAction
{
    /**
     * Update an existing customer record.
     *
     * @param  array{name?: string, email?: string, phone?: string}  $data
     *
     * @throws ValidationException
     */
    public function execute(Customer $customer, array $data): Customer
    {
        if (isset($data['email'])) {
            $email = trim(strtolower($data['email']));
            $existing = Customer::where('email', $email)->where('id', '!=', $customer->id)->first();
            if ($existing) {
                throw ValidationException::withMessages([
                    'email' => "Email '{$email}' sudah digunakan oleh pelanggan lain.",
                ]);
            }
            $data['email'] = $email;
        }

        if (isset($data['phone'])) {
            $data['phone'] = trim($data['phone']);
        }

        $customer->update($data);

        return $customer->fresh();
    }
}
