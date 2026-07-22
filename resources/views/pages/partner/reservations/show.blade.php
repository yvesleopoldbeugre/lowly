@php
    $badgeVariant = [
        'en_attente' => 'warning',
        'confirmee' => 'success',
        'refusee' => 'danger',
        'contre_proposee' => 'warning',
        'expiree' => 'neutral',
    ];

    $reservableTitle = fn ($reservable) => $reservable instanceof \App\Domains\Catalogue\Models\Vehicle
        ? "{$reservable->brand} {$reservable->model}"
        : $reservable->title;
@endphp

<x-layouts::app :title="$title">
    <a href="{{ route('partner.reservations.index') }}" class="mb-4 inline-block text-sm text-neutral-500 hover:text-neutral-900">← Réservations</a>

    <x-ui.card>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-lg font-semibold text-neutral-900">{{ $reservableTitle($reservation->reservable) }}</h1>
                <p class="mt-1 text-sm text-neutral-500">
                    {{ $reservation->period['start']->format('d/m/Y') }} → {{ $reservation->period['end']->format('d/m/Y') }}
                    · {{ $reservation->nights_count }} journée(s) · {{ number_format($reservation->total_amount, 0, ',', ' ') }} FCFA
                </p>
                <p class="mt-1 text-sm text-neutral-500">Client : {{ $reservation->client->full_name }}</p>
            </div>
            <x-ui.badge :variant="$badgeVariant[$reservation->status] ?? 'neutral'">{{ $reservation->status }}</x-ui.badge>
        </div>

        @if ($reservation->status === 'contre_proposee' && $reservation->counterOffer)
            @php $counterOffer = $reservation->counterOffer; @endphp
            <div class="mt-6 rounded-lg border border-warning-100 bg-warning-50 p-4">
                <h2 class="mb-2 text-sm font-semibold text-neutral-900">Contre-proposition envoyée</h2>
                <p class="text-sm text-neutral-700">{{ $reservableTitle($counterOffer->proposedReservable) }}</p>
                <p class="text-sm text-neutral-500">
                    {{ $counterOffer->proposed_period['start']->format('d/m/Y') }} → {{ $counterOffer->proposed_period['end']->format('d/m/Y') }}
                    · en attente de réponse du client
                </p>
            </div>
        @endif

        @if ($reservation->status === 'en_attente')
            <div x-data="partnerReservationResponse('{{ $reservation->id }}')" class="mt-6">
                <template x-if="generalError"><p class="mb-3 text-sm text-danger-600" x-text="generalError"></p></template>

                <div class="flex gap-2">
                    <x-ui.button x-bind:disabled="loading" @click="accept()">Accepter</x-ui.button>
                    <x-ui.button variant="secondary" x-bind:disabled="loading" @click="reject()">Refuser</x-ui.button>
                    <x-ui.button variant="secondary" x-bind:disabled="loading" @click="showCounterOfferForm = !showCounterOfferForm">
                        Refuser avec contre-proposition
                    </x-ui.button>
                </div>

                <div x-show="showCounterOfferForm" x-cloak class="mt-4 flex flex-col gap-3 rounded-lg border border-neutral-200 p-4">
                    <label class="block">
                        <span class="mb-1 block text-xs font-medium text-neutral-500">Bien alternatif</span>
                        <select x-model="proposed_reservable_id" @change="proposed_reservable_type = $event.target.selectedOptions[0].dataset.type" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm">
                            <option value="">Choisir un bien…</option>
                            @foreach ($residences as $residence)
                                @if ($residence->id !== $reservation->reservable_id)
                                    <option value="{{ $residence->id }}" data-type="residence">{{ $residence->title }} — résidence</option>
                                @endif
                            @endforeach
                            @foreach ($vehicles as $vehicle)
                                @if ($vehicle->id !== $reservation->reservable_id)
                                    <option value="{{ $vehicle->id }}" data-type="vehicle">{{ $vehicle->brand }} {{ $vehicle->model }} — véhicule</option>
                                @endif
                            @endforeach
                        </select>
                    </label>

                    <p class="text-xs text-neutral-500">Laissez les dates vides pour reprendre celles de la demande initiale.</p>
                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.input label="Du (optionnel)" type="date" x-model="start_date" />
                        <x-ui.input label="Au (optionnel)" type="date" x-model="end_date" />
                    </div>
                    <template x-if="errors.proposed_reservable_id || errors.start_date || errors.end_date">
                        <p class="text-xs text-danger-600">Bien ou période invalide.</p>
                    </template>

                    <x-ui.button x-bind:disabled="loading || !proposed_reservable_id" @click="submitCounterOffer()" class="self-start">
                        Envoyer la contre-proposition
                    </x-ui.button>
                </div>
            </div>
        @endif
    </x-ui.card>
</x-layouts::app>
