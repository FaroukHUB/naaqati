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
                    <span class="absolute -top-2 -right-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1 text-[10px] font-bold text-white" style="background-color:#B76E79;">
                        {{ $count }}
                    </span>
                @endif
            </span>
        BLADE;
    }
}
