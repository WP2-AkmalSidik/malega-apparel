<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\AuthenticateUserAction;
use App\Livewire\Forms\LoginForm;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Masuk Backoffice | Malega Apparel')]
#[Layout('layouts.auth')]
class Login extends Component
{
    public LoginForm $form;

    /**
     * Handle backoffice user authentication submission.
     */
    public function login(AuthenticateUserAction $action): void
    {
        $this->form->authenticate($action);

        session()->flash('success', 'Otentikasi berhasil. Selamat datang kembali.');

        $this->redirectIntended(default: route('dashboard'), navigate: true);
    }

    /**
     * Render the Livewire component view.
     */
    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
