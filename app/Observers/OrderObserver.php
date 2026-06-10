<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderNotifier;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class OrderObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private readonly OrderNotifier $notifier) {}

    public function created(Order $order): void
    {
        $this->notifier->notifyNewOrder($order);
    }
}
