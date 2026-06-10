<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use App\Models\NewsletterSubscription;
use App\Support\Phone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.shop-layout')]
class Register extends Component
{
    public string $nom = '';
    public string $indicatif = '213';
    public string $telephone = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $newsletter = true;

    public function inscrire()
    {
        $this->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'indicatif' => ['required', 'in:' . implode(',', array_keys(Phone::INDICATIFS))],
            'telephone' => ['required', 'string', 'min:6', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], attributes: [
            'nom' => 'nom', 'telephone' => 'téléphone', 'password' => 'mot de passe',
        ]);

        $telephone = Phone::international($this->indicatif, $this->telephone);

        $customer = Customer::where('telephone', $telephone)->first();

        if ($customer && $customer->password) {
            $this->addError('telephone', 'Un compte existe déjà avec ce numéro. Connectez-vous.');
            return;
        }

        if ($customer) {
            // Compte "invité" créé lors d'une commande : on le réclame.
            $customer->update([
                'nom' => $this->nom,
                'email' => $this->email ?: $customer->email,
                'password' => Hash::make($this->password),
                'newsletter' => $this->newsletter,
            ]);
        } else {
            $customer = Customer::create([
                'nom' => $this->nom,
                'telephone' => $telephone,
                'email' => $this->email ?: null,
                'password' => Hash::make($this->password),
                'newsletter' => $this->newsletter,
            ]);
        }

        if ($this->newsletter && $this->email) {
            NewsletterSubscription::firstOrCreate(
                ['email' => $this->email],
                ['customer_id' => $customer->id],
            );
        }

        Auth::guard('customer')->login($customer, remember: true);

        return redirect()->route('shop.account');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
