@props(['title' => 'Naaqati — Riadi City'])
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @livewireStyles
</head>
<body class="h-full bg-stone-50 text-stone-800 antialiased" style="font-family: 'Figtree', sans-serif;">

    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-stone-200">
        <div class="mx-auto max-w-5xl px-4 h-16 flex items-center justify-between">
            <a href="{{ route('shop.catalog') }}" class="flex items-center gap-2">
                <span class="text-2xl font-bold tracking-tight" style="color:#B76E79;">Naaqati</span>
                <span class="hidden sm:inline text-xs text-stone-400">· Riadi City</span>
            </a>
            <a href="{{ route('shop.cart') }}" class="relative inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium text-white transition hover:opacity-90" style="background-color:#B76E79;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="hidden sm:inline">Panier</span>
                <livewire:storefront.cart-counter />
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-6">
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-stone-200 py-8 text-center text-xs text-stone-400">
        <p>Naaqati — Point relais Riadi City · Retrait sur place</p>
    </footer>

    @livewireScripts
</body>
</html>
