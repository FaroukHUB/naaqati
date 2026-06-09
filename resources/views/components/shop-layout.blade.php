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
    <meta name="theme-color" content="#B76E79">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Naaqati">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @livewireStyles
</head>
<body class="h-full bg-stone-50 text-stone-800 antialiased" style="font-family: 'Figtree', sans-serif;">

    {{-- Header allégé --}}
    <header class="sticky top-0 z-30 bg-white/85 backdrop-blur border-b border-stone-100">
        <div class="mx-auto max-w-3xl px-4 h-14 flex items-center justify-center relative">
            <a href="{{ route('shop.catalog') }}" class="flex items-center gap-1.5">
                <span class="text-xl font-extrabold tracking-tight" style="color:#B76E79;">Naaqati</span>
            </a>
            <span class="absolute right-4 inline-flex items-center gap-1 rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-medium text-stone-500">
                <span class="h-1.5 w-1.5 rounded-full" style="background-color:#B76E79;"></span> Riadi City
            </span>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-5 pb-28">
        {{ $slot }}
    </main>

    {{-- Bandeau de navigation bas (app mobile) --}}
    <nav class="fixed bottom-0 inset-x-0 z-40 border-t border-stone-200 bg-white/95 backdrop-blur"
         style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="mx-auto max-w-3xl grid grid-cols-3 items-end px-6">
            {{-- Accueil --}}
            <a href="{{ route('shop.catalog') }}" @class(['flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-medium', 'text-stone-400' => !request()->routeIs('shop.catalog'), '' => request()->routeIs('shop.catalog')])
               @style(['color:#B76E79' => request()->routeIs('shop.catalog')])>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                Accueil
            </a>

            {{-- Assistante (mise en avant, centre) --}}
            <a href="{{ route('shop.assistante') }}" class="flex flex-col items-center -mt-5">
                <span class="flex h-14 w-14 items-center justify-center rounded-full text-white shadow-lg ring-4 ring-stone-50" style="background:linear-gradient(135deg,#C98B96,#9B5563);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5h8M8 14h5m-9 6l3.5-2H17a3 3 0 003-3V7a3 3 0 00-3-3H7a3 3 0 00-3 3v13z"/></svg>
                </span>
                <span class="mt-1 text-[11px] font-semibold" style="color:#B76E79;">Assistante</span>
            </a>

            {{-- Panier --}}
            <a href="{{ route('shop.cart') }}" @class(['relative flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-medium', 'text-stone-400' => !request()->routeIs('shop.cart')])
               @style(['color:#B76E79' => request()->routeIs('shop.cart')])>
                <span class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    <livewire:storefront.cart-counter />
                </span>
                Panier
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
