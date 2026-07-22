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

    $helperText = [
        'en_attente' => "Votre demande a été transmise au partenaire. Il dispose d'un délai de réponse configuré par la plateforme.",
        'confirmee' => 'Réservation confirmée — le calendrier du bien est bloqué pour ces dates.',
        'refusee' => 'Cette demande a été refusée. Vous pouvez soumettre une nouvelle demande sur une autre annonce.',
        'contre_proposee' => 'Le partenaire vous propose une alternative — voir ci-dessous.',
        'expiree' => 'Cette contre-proposition a expiré faute de réponse dans le délai imparti.',
    ][$reservation->status] ?? '';
@endphp

<x-layouts::guest :title="$title">
    <div class="mx-auto max-w-2xl px-4 py-8">
        <a href="{{ route('reservations.index') }}" class="mb-4 inline-block text-sm text-neutral-500 hover:text-neutral-900">← Mes réservations</a>

        <x-ui.card>
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-neutral-900">{{ $reservableTitle($reservation->reservable) }}</h1>
                    <p class="mt-1 text-sm text-neutral-500">
                        {{ $reservation->period['start']->format('d/m/Y') }} → {{ $reservation->period['end']->format('d/m/Y') }}
                        · {{ $reservation->nights_count }} journée(s) · {{ number_format($reservation->total_amount, 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <x-ui.badge :variant="$badgeVariant[$reservation->status] ?? 'neutral'">{{ $reservation->status }}</x-ui.badge>
            </div>

            @if ($helperText)
                <p class="mt-4 text-sm text-neutral-600">{{ $helperText }}</p>
            @endif

            @if ($reservation->status === 'contre_proposee' && $reservation->counterOffer)
                @php $counterOffer = $reservation->counterOffer; @endphp
                <div
                    x-data="counterOfferResponse('{{ $reservation->id }}', '{{ $counterOffer->id }}')"
                    class="mt-6 rounded-lg border border-warning-100 bg-warning-50 p-4"
                >
                    <h2 class="mb-2 text-sm font-semibold text-neutral-900">Alternative proposée</h2>
                    <p class="text-sm text-neutral-700">{{ $reservableTitle($counterOffer->proposedReservable) }}</p>
                    <p class="text-sm text-neutral-500">
                        {{ $counterOffer->proposed_period['start']->format('d/m/Y') }} → {{ $counterOffer->proposed_period['end']->format('d/m/Y') }}
                    </p>

                    <template x-if="generalError"><p class="mt-3 text-xs text-danger-600" x-text="generalError"></p></template>

                    <div class="mt-4 flex gap-2">
                        <x-ui.button x-bind:disabled="loading" @click="respond('accept')">Accepter la proposition</x-ui.button>
                        <x-ui.button variant="secondary" x-bind:disabled="loading" @click="respond('reject')">Refuser la proposition</x-ui.button>
                    </div>
                </div>
            @endif
        </x-ui.card>

        @if ($reservation->statusHistory->isNotEmpty())
            <x-ui.card class="mt-6">
                <h2 class="mb-3 text-sm font-semibold text-neutral-900">Historique</h2>
                <ol class="flex flex-col gap-2 text-sm text-neutral-600">
                    @foreach ($reservation->statusHistory->sortBy('changed_at') as $entry)
                        <li>{{ $entry->new_status }} — {{ $entry->changed_at->format('d/m/Y H:i') }}</li>
                    @endforeach
                </ol>
            </x-ui.card>
        @endif
    </div>
</x-layouts::guest>
