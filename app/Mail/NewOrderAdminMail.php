<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('customer', 'packages.packaging', 'packages.items');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouvelle commande ' . $this->order->numero);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-order-admin');
    }
}
