<?php

namespace App\Livewire;

use App\Models\NewsletterSubscription;
use Livewire\Component;

class NewsletterForm extends Component
{
    public string $email = '';
    public bool $inscrit = false;

    public function sinscrire()
    {
        $this->validate(
            ['email' => ['required', 'email', 'max:190']],
            attributes: ['email' => 'email'],
        );

        NewsletterSubscription::firstOrCreate(['email' => $this->email]);

        $this->inscrit = true;
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.newsletter-form');
    }
}
