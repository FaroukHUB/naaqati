<div class="mx-auto max-w-sm">
    <div class="rounded-3xl border border-stone-100 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-extrabold text-stone-900">Créer un compte</h1>
        <p class="mt-1 text-sm text-stone-500">Suivez vos commandes et gagnez du temps au retrait.</p>

        <form wire:submit="inscrire" class="mt-6 space-y-4">
            @php $cls = 'w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#B76E79]'; @endphp
            <div>
                <label class="mb-1 block text-sm font-semibold text-stone-700">Nom complet <span style="color:#B76E79;">*</span></label>
                <input type="text" wire:model="nom" class="{{ $cls }}">
                @error('nom') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-stone-700">Téléphone / WhatsApp <span style="color:#B76E79;">*</span></label>
                <div class="flex gap-2">
                    <select wire:model="indicatif" class="w-28 shrink-0 rounded-xl border-2 border-stone-200 bg-white px-2 py-3.5 text-base text-stone-900 outline-none focus:border-[#B76E79]">
                        @foreach (\App\Support\Phone::INDICATIFS as $code => $lbl)
                            <option value="{{ $code }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                    <input type="tel" wire:model="telephone" placeholder="555 12 34 56" class="{{ $cls }} flex-1">
                </div>
                @error('telephone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-stone-700">Email <span class="font-normal text-stone-400">(optionnel)</span></label>
                <input type="email" wire:model="email" class="{{ $cls }}">
                @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-stone-700">Mot de passe <span style="color:#B76E79;">*</span></label>
                <input type="password" wire:model="password" class="{{ $cls }}">
                @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-stone-700">Confirmer le mot de passe <span style="color:#B76E79;">*</span></label>
                <input type="password" wire:model="password_confirmation" class="{{ $cls }}">
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" wire:model="newsletter" class="rounded border-stone-300 text-[#B76E79] focus:ring-0">
                Recevoir les nouveautés et offres Naaqati
            </label>
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-xl py-3 text-sm font-semibold text-white transition hover:opacity-90 disabled:opacity-50" style="background-color:#B76E79;">
                Créer mon compte
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-stone-500">
            Déjà un compte ?
            <a href="{{ route('shop.login') }}" wire:navigate class="font-semibold" style="color:#B76E79;">Se connecter</a>
        </p>
    </div>
</div>
