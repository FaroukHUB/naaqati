<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.shop-layout')]
class Login extends Component
{
    public string $telephone = '';
    public string $password = '';
    public bool $remember = true;

    public function connecter()
    {
        $this->validate([
            'telephone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], attributes: [
            'telephone' => 'téléphone', 'password' => 'mot de passe',
        ]);

        $ok = Auth::guard('customer')->attempt([
            'telephone' => $this->telephone,
            'password' => $this->password,
        ], $this->remember);

        if (! $ok) {
            $this->addError('telephone', 'Numéro ou mot de passe incorrect.');
            return;
        }

        session()->regenerate();

        return redirect()->route('shop.account');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
