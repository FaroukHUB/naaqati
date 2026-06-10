<div class="mx-auto max-w-sm">
    <div class="rounded-3xl border border-stone-100 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-extrabold text-stone-900">Connexion</h1>
        <p class="mt-1 text-sm text-stone-500">Accédez à votre compte Naaqati.</p>

        <form wire:submit="connecter" class="mt-6 space-y-4">
            <div>
                <label class="mb-1 block text-sm font-semibold text-stone-700">Téléphone</label>
                <div class="flex gap-2">
                    <select wire:model="indicatif" class="w-28 shrink-0 rounded-xl border-2 border-stone-200 bg-white px-2 py-3.5 text-base text-stone-900 outline-none focus:border-[#B76E79]">
                        @foreach (\App\Support\Phone::INDICATIFS as $code => $lbl)
                            <option value="{{ $code }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                    <input type="tel" wire:model="telephone" placeholder="555 12 34 56"
                           class="flex-1 rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#B76E79]">
                </div>
                @error('telephone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-stone-700">Mot de passe</label>
                <input type="password" wire:model="password"
                       class="w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 shadow-sm outline-none transition focus:border-[#B76E79]">
                @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" wire:model="remember" class="rounded border-stone-300 text-[#B76E79] focus:ring-0">
                Se souvenir de moi
            </label>
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-xl py-3 text-sm font-semibold text-white transition hover:opacity-90 disabled:opacity-50" style="background-color:#B76E79;">
                Se connecter
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-stone-500">
            Pas encore de compte ?
            <a href="{{ route('shop.register') }}" wire:navigate class="font-semibold" style="color:#B76E79;">Créer un compte</a>
        </p>
    </div>
</div>
