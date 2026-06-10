<x-filament-panels::page>
    <div class="max-w-xl space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Activez les notifications pour recevoir une alerte sur cet appareil
            à chaque nouvelle commande — même lorsque l'application est fermée.
            Idéal sur votre téléphone (installez d'abord Naaqati sur l'écran d'accueil).
        </p>

        @if (! $vapidPublicKey)
            <div class="rounded-lg bg-amber-50 p-4 text-sm text-amber-700">
                Les clés de notification (VAPID) ne sont pas encore configurées dans le fichier <code>.env</code>.
            </div>
        @else
            <x-filament::button id="btn-push" icon="heroicon-o-bell-alert">
                Activer les notifications sur cet appareil
            </x-filament::button>
            <p id="push-status" class="text-sm font-medium"></p>
        @endif
    </div>

    @if ($vapidPublicKey)
        <script>
            (function () {
                const vapidPublicKey = @json($vapidPublicKey);
                const csrf = @json(csrf_token());
                const subscribeUrl = @json(route('push.subscribe'));
                const status = (m, ok) => { const el = document.getElementById('push-status'); el.textContent = m; el.style.color = ok ? '#059669' : '#dc2626'; };

                function urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const raw = atob(base64);
                    return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
                }

                document.getElementById('btn-push').addEventListener('click', async () => {
                    try {
                        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                            return status('Votre navigateur ne supporte pas les notifications push.', false);
                        }
                        const perm = await Notification.requestPermission();
                        if (perm !== 'granted') return status('Permission refusée.', false);

                        const reg = await navigator.serviceWorker.register('/sw.js');
                        await navigator.serviceWorker.ready;

                        const sub = await reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
                        });

                        const res = await fetch(subscribeUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: JSON.stringify(sub.toJSON()),
                        });
                        if (res.ok) status('✓ Notifications activées sur cet appareil.', true);
                        else status('Erreur lors de l\'enregistrement.', false);
                    } catch (e) {
                        status('Erreur : ' + e.message, false);
                    }
                });
            })();
        </script>
    @endif
</x-filament-panels::page>
