@props(['title' => 'Naaqati — Riadi City'])
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#A1763C">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Naaqati">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @livewireStyles
</head>
<body class="h-full overflow-x-hidden bg-stone-50 text-stone-800 antialiased" style="font-family: 'Figtree', sans-serif;">

    {{-- Header allégé --}}
    <header class="sticky top-0 z-30 bg-[#FBF7F0]/90 backdrop-blur border-b border-[#EADfce]">
        <div class="mx-auto max-w-3xl px-4 h-16 flex items-center justify-center relative">
            <a href="{{ route('shop.catalog') }}" class="flex items-center">
                @if (file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="Naaqati" class="h-11 w-auto">
                @else
                    <span class="flex flex-col items-center leading-none">
                        <span class="text-xl font-extrabold tracking-[0.25em]" style="color:#A1763C;">NAAQATI</span>
                        <span class="mt-0.5 text-[9px] tracking-[0.3em] text-stone-400">PRODUITS DU SAHARA</span>
                    </span>
                @endif
            </a>
            <span class="absolute right-4 inline-flex items-center gap-1 rounded-full bg-white/70 px-2.5 py-1 text-[11px] font-medium text-stone-500">
                <span class="h-1.5 w-1.5 rounded-full" style="background-color:#A1763C;"></span> Riadi City
            </span>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-6">
        {{ $slot }}
    </main>

    <footer class="mt-10 border-t border-[#EADFCE] bg-[#FBF7F0] px-4 pt-8 pb-28 text-center text-xs text-stone-400">
        @if (file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" alt="Naaqati" class="mx-auto mb-3 h-10 w-auto">
        @else
            <p class="text-base font-extrabold tracking-[0.25em] text-[#A1763C]">NAAQATI</p>
        @endif
        <div class="mb-3 mt-2 flex flex-wrap items-center justify-center gap-x-5 gap-y-1 font-medium text-stone-500">
            <a href="{{ route('shop.catalog') }}" wire:navigate class="hover:text-[#A1763C]">Boutique</a>
            <a href="{{ route('shop.assistante') }}" class="hover:text-[#A1763C]">Assistante</a>
            <a href="{{ route('shop.account') }}" class="hover:text-[#A1763C]">Mon compte</a>
        </div>
        <p>Produits du Sahara · Point relais Riadi City</p>
        <p class="mt-1">Retrait sur place · Paiement à la récupération</p>
        <p class="mt-3 text-[10px] text-stone-300">© {{ date('Y') }} Naaqati</p>
    </footer>

    @php
        $tab = 'flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-medium';
        $loggedIn = auth('customer')->check();
    @endphp
    {{-- Bandeau de navigation bas (app mobile) --}}
    <nav class="fixed bottom-0 inset-x-0 z-40 border-t border-stone-200 bg-white/95 backdrop-blur"
         style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="mx-auto grid max-w-3xl grid-cols-4 px-2">
            {{-- Accueil --}}
            <a href="{{ route('shop.catalog') }}" wire:navigate
               @class([$tab, 'text-stone-400' => !request()->routeIs(['shop.catalog', 'shop.product'])])
               @style(['color:#A1763C' => request()->routeIs(['shop.catalog', 'shop.product'])])>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                Accueil
            </a>

            {{-- Assistante --}}
            <a href="{{ route('shop.assistante') }}"
               @class([$tab, 'text-stone-400' => !request()->routeIs('shop.assistante')])
               @style(['color:#A1763C' => request()->routeIs('shop.assistante')])>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5h8M8 14h5m-9 6l3.5-2H17a3 3 0 003-3V7a3 3 0 00-3-3H7a3 3 0 00-3 3v13z"/></svg>
                Assistante
            </a>

            {{-- Panier --}}
            <a href="{{ route('shop.cart') }}" wire:navigate
               @class([$tab, 'text-stone-400' => !request()->routeIs('shop.cart')])
               @style(['color:#A1763C' => request()->routeIs('shop.cart')])>
                <span class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    <livewire:storefront.cart-counter />
                </span>
                Panier
            </a>

            {{-- Compte --}}
            <a href="{{ $loggedIn ? route('shop.account') : route('shop.login') }}"
               @class([$tab, 'text-stone-400' => !request()->routeIs(['shop.account', 'shop.login', 'shop.register'])])
               @style(['color:#A1763C' => request()->routeIs(['shop.account', 'shop.login', 'shop.register'])])>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                {{ $loggedIn ? 'Compte' : 'Connexion' }}
            </a>
        </div>
    </nav>

    @livewireScripts
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
    </script>
</body>
</html>
