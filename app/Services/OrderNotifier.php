<?php

namespace App\Services;

use App\Mail\NewOrderAdminMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\PushSubscription;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Minishlink\WebPush\Subscription as WebPushSubscription;
use Minishlink\WebPush\WebPush;

/**
 * Alerte « nouvelle commande » sur tous les canaux configurés.
 * Chaque canal est isolé : un échec n'empêche ni les autres ni la commande.
 */
class OrderNotifier
{
    public function notifyNewOrder(Order $order): void
    {
        $order->loadMissing('customer');

        $this->safe(fn () => $this->filamentBell($order));
        $this->safe(fn () => $this->telegram($order));
        $this->safe(fn () => $this->emails($order));
        $this->safe(fn () => $this->webPush($order));
    }

    private function safe(callable $fn): void
    {
        try {
            $fn();
        } catch (\Throwable $e) {
            Log::warning('OrderNotifier: ' . $e->getMessage());
        }
    }

    private function resume(Order $order): string
    {
        $total = number_format($order->total / 100, 0, ',', ' ') . ' DA';
        $retrait = $order->date_retrait
            ? \Carbon\Carbon::parse($order->date_retrait)->isoFormat('D MMM')
            : 'à définir';
        if ($order->creneau_retrait) {
            $retrait .= ' · ' . str_replace('_', ' ', $order->creneau_retrait);
        }

        return "{$order->customer?->nom} — {$total}\nRetrait : {$retrait}\nRécupère : " . ($order->recuperateur ?? '—');
    }

    private function filamentBell(Order $order): void
    {
        $admins = User::where('actif', true)->get();
        if ($admins->isEmpty()) {
            return;
        }

        FilamentNotification::make()
            ->title('Nouvelle commande ' . $order->numero)
            ->body($order->customer?->nom . ' — ' . number_format($order->total / 100, 0, ',', ' ') . ' DA')
            ->icon('heroicon-o-shopping-bag')
            ->iconColor('success')
            ->sendToDatabase($admins);
    }

    private function telegram(Order $order): void
    {
        $token = config('services.telegram.token');
        $chat = config('services.telegram.chat_id');
        if (! $token || ! $chat) {
            return;
        }

        $text = "🛍️ <b>Nouvelle commande {$order->numero}</b>\n" . $this->resume($order);

        Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chat,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    private function emails(Order $order): void
    {
        // Pas d'envoi si le mailer n'est pas configuré (driver "log" par défaut).
        if (config('mail.default') === 'log' || ! config('mail.mailers.smtp.host')) {
            return;
        }

        if ($admin = config('naaqati.admin_email')) {
            Mail::to($admin)->send(new NewOrderAdminMail($order));
        }

        if ($order->customer?->email) {
            Mail::to($order->customer->email)->send(new OrderConfirmationMail($order));
        }
    }

    private function webPush(Order $order): void
    {
        $public = config('services.webpush.public_key');
        $private = config('services.webpush.private_key');
        if (! $public || ! $private) {
            return;
        }

        $subs = PushSubscription::all();
        if ($subs->isEmpty()) {
            return;
        }

        $webPush = new WebPush(['VAPID' => [
            'subject' => config('services.webpush.subject'),
            'publicKey' => $public,
            'privateKey' => $private,
        ]]);

        $payload = json_encode([
            'title' => 'Nouvelle commande ' . $order->numero,
            'body' => $this->resume($order),
            'url' => '/admin/orders',
        ]);

        foreach ($subs as $sub) {
            $webPush->queueNotification(
                WebPushSubscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys' => ['p256dh' => $sub->p256dh, 'auth' => $sub->auth],
                ]),
                $payload,
            );
        }

        foreach ($webPush->flush() as $report) {
            // Nettoie les abonnements expirés / invalides.
            if (! $report->isSuccess() && $report->isSubscriptionExpired()) {
                PushSubscription::where('endpoint_hash', hash('sha256', $report->getEndpoint()))->delete();
            }
        }
    }
}
