<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    public int $count = 0;

    public function mount(CartService $cart): void
    {
        $this->count = $cart->count();
    }

    #[On('cart-updated')]
    public function refreshCount(CartService $cart): void
    {
        $this->count = $cart->count();
    }

    public function render()
    {
        return <<<'BLADE'
            <span>
                @if ($count > 0)
                    <span class="absolute -top-1 -right-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-white px-1 text-xs font-bold" style="color:#B76E79;">
                        {{ $count }}
                    </span>
                @endif
            </span>
        BLADE;
    }
}
