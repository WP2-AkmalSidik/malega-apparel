<?php

namespace App\Livewire\Forms;

use App\Actions\Auth\AuthenticateUserAction;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required', message: 'Alamat email wajib diisi.')]
    #[Validate('email', message: 'Format alamat email tidak valid.')]
    public string $email = '';

    #[Validate('required', message: 'Password wajib diisi.')]
    #[Validate('string')]
    #[Validate('min:6', message: 'Password minimal 6 karakter.')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Attempt to authenticate the user using the Form data.
     */
    public function authenticate(AuthenticateUserAction $action): User
    {
        $this->validate();

        return $action->execute([
            'email' => $this->email,
            'password' => $this->password,
            'remember' => $this->remember,
        ]);
    }
}
