<div>
    <h1 class="mb-5 text-2xl font-bold text-stone-900">Finaliser ma commande</h1>

    <form wire:submit="valider" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">

            {{-- Coordonnées --}}
            <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-stone-900">Mes coordonnées</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-stone-700">Nom complet <span style="color:#B76E79;">*</span></label>
                        <input type="text" wire:model="nom" class="w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#B76E79]">
                        @error('nom') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-stone-700">Téléphone / WhatsApp <span style="color:#B76E79;">*</span></label>
                        <input type="tel" wire:model="telephone" placeholder="0555 12 34 56" class="w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#B76E79]">
                        <p class="mt-1 text-xs text-stone-400">C'est sur ce numéro que vous recevrez la notification WhatsApp.</p>
                        @error('telephone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-semibold text-stone-700">Email <span class="font-normal text-stone-400">(optionnel)</span></label>
                        <input type="email" wire:model="email" class="w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#B76E79]">
                        @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Retrait --}}
            <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                <h2 class="mb-1 text-base font-bold text-stone-900">Quand venez-vous récupérer ?</h2>
                <p class="mb-4 text-xs text-stone-400">Retrait sur place à Riadi City (jusqu'à 7 jours). Pas encore sûre ? Choisissez « Je ne sais pas ».</p>

                {{-- Date --}}
                <div class="flex flex-wrap gap-2">
                    @foreach ($dates as $date => $creneaux)
                        @php $dateActive = $dateMode === 'date' && $date_retrait === $date; @endphp
                        <button type="button" wire:click="selectDate('{{ $date }}')"
                                @class(['rounded-xl px-3 py-2 text-sm font-medium transition', 'text-white' => $dateActive, 'bg-white text-stone-600 border border-stone-200 hover:border-stone-300' => !$dateActive])
                                @style(['background-color:#B76E79' => $dateActive])>
                            {{ \Carbon\Carbon::parse($date)->isoFormat('ddd D MMM') }}
                        </button>
                    @endforeach
                    <button type="button" wire:click="setDateMode('inconnue')"
                            @class(['rounded-xl px-3 py-2 text-sm font-medium transition', 'text-white' => $dateMode === 'inconnue', 'bg-white text-stone-600 border border-stone-200 hover:border-stone-300' => $dateMode !== 'inconnue'])
                            @style(['background-color:#B76E79' => $dateMode === 'inconnue'])>
                        Je ne sais pas encore
                    </button>
                </div>
                @error('date_retrait') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror

                {{-- Heure (si une date est choisie) --}}
                @if ($dateMode === 'date' && $date_retrait && isset($dates[$date_retrait]))
                    <h3 class="mb-2 mt-5 text-sm font-semibold text-stone-700">Heure</h3>
                    <div class="mb-3 flex flex-wrap gap-2">
                        @foreach (['creneau' => 'Créneau', 'precise' => 'Heure précise', 'inconnue' => 'Je ne sais pas'] as $m => $lbl)
                            <button type="button" wire:click="setHeureMode('{{ $m }}')"
                                    @class(['rounded-full px-3 py-1.5 text-xs font-medium transition', 'text-white' => $heureMode === $m, 'bg-stone-100 text-stone-500 hover:bg-stone-200' => $heureMode !== $m])
                                    @style(['background-color:#B76E79' => $heureMode === $m])>{{ $lbl }}</button>
                        @endforeach
                    </div>

                    @if ($heureMode === 'creneau')
                        <div class="flex flex-wrap gap-2">
                            @foreach ($dates[$date_retrait] as $c)
                                <button type="button" wire:click="selectCreneau('{{ $c }}')"
                                        @class(['rounded-xl px-4 py-2 text-sm font-medium transition', 'text-white' => $creneau === $c, 'bg-white text-stone-600 border border-stone-200 hover:border-stone-300' => $creneau !== $c])
                                        @style(['background-color:#B76E79' => $creneau === $c])>
                                    {{ $creneauxLabels[$c] ?? ucfirst($c) }}
                                </button>
                            @endforeach
                        </div>
                        @error('creneau') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    @elseif ($heureMode === 'precise')
                        <input type="time" wire:model="heurePrecise"
                               class="rounded-xl border-2 border-stone-200 bg-white px-4 py-2.5 text-base text-stone-900 outline-none focus:border-[#B76E79]">
                        @error('heurePrecise') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    @else
                        <p class="text-xs text-stone-400">Pas de souci — vous nous préciserez l'heure le moment venu.</p>
                    @endif
                @endif
            </div>

            {{-- Qui récupère --}}
            <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                <label class="mb-1 block text-sm font-bold text-stone-900">Qui viendra récupérer ? <span class="font-normal text-stone-400">(optionnel)</span></label>
                <input type="text" wire:model="recuperateur" placeholder="Vous-même, votre fille, un proche…"
                       class="w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#B76E79]">
                <p class="mt-2 text-xs text-stone-400">Pour votre confort, si une autre personne récupère, la commande pourra être déposée avec soin devant votre porte.</p>
            </div>

            {{-- Commentaire --}}
            <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                <label class="mb-1 block text-sm font-bold text-stone-900">Commentaire <span class="font-normal text-stone-400">(optionnel)</span></label>
                <textarea wire:model="commentaire" rows="3" class="w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3.5 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#B76E79]" placeholder="Une précision pour votre commande ?"></textarea>
            </div>
        </div>

        {{-- Récap + validation --}}
        <div class="lg:col-span-1">
            <div class="sticky top-20 rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-stone-900">Votre commande</h2>
                <div class="space-y-3 text-sm">
                    @foreach ($packages as $package)
                        @if ($package['lines']->isNotEmpty())
                            <div>
                                @if ($packages->count() > 1)
                                    <p class="mb-1 text-xs font-semibold text-stone-500">
                                        🎁 Paquet {{ $package['index'] }}{{ $package['destinataire'] ? ' · '.$package['destinataire'] : '' }}
                                        @if ($package['packaging']) <span class="font-normal text-stone-400">({{ $package['packaging']->nom }})</span> @endif
                                    </p>
                                @endif
                                @foreach ($package['lines'] as $line)
                                    <div class="flex justify-between text-stone-600">
                                        <span class="truncate pr-2">{{ $line['quantite'] }}× {{ $line['product']->nom }}</span>
                                        <span class="shrink-0">{{ number_format($line['total_ligne'] / 100, 0, ',', ' ') }} DA</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    <div class="my-3 border-t border-stone-100"></div>
                    <div class="flex justify-between text-base font-bold text-stone-900">
                        <span>Total</span>
                        <span>{{ number_format($total / 100, 0, ',', ' ') }} DA</span>
                    </div>
                </div>
                <button type="submit" wire:loading.attr="disabled"
                        class="mt-5 w-full rounded-xl py-3 text-sm font-semibold text-white transition hover:opacity-90 disabled:opacity-50"
                        style="background-color:#B76E79;">
                    <span wire:loading.remove>Confirmer la commande</span>
                    <span wire:loading>Traitement…</span>
                </button>
                <p class="mt-3 text-center text-xs text-stone-400">Paiement sur place lors du retrait.</p>
            </div>
        </div>
    </form>
</div>
