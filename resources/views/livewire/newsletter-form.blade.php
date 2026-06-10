<div class="mx-auto max-w-sm">
    @if ($inscrit)
        <p class="text-sm font-medium" style="color:#A1763C;">Merci, vous êtes inscrit·e à la newsletter ! 🌸</p>
    @else
        <p class="mb-2 text-xs font-medium uppercase tracking-wider text-stone-400">Newsletter</p>
        <form wire:submit="sinscrire" class="flex gap-2">
            <input type="email" wire:model="email" placeholder="Votre email"
                   class="flex-1 rounded-xl border-2 border-stone-200 bg-white px-3 py-2 text-sm text-stone-900 placeholder-stone-400 outline-none focus:border-[#A1763C]">
            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-semibold text-white" style="background-color:#A1763C;">OK</button>
        </form>
        @error('email') <span class="mt-1 block text-xs text-red-500">{{ $message }}</span> @enderror
    @endif
</div>
