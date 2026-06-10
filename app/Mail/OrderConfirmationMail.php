<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('customer', 'relais', 'packages.packaging', 'packages.items');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre commande Naaqati ' . $this->order->numero);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-confirmation');
    }
}
