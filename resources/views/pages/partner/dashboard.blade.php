@php
    $residences = $stats['residences'];
    $vehicles = $stats['vehicles'];
    $cards = [
        ['label' => 'Résidences publiées', 'value' => $residences['publiee'] ?? 0],
        ['label' => 'Résidences en attente de validation', 'value' => $residences['en_validation'] ?? 0],
        ['label' => 'Véhicules publiés', 'value' => $vehicles['publie'] ?? 0],
        ['label' => 'Véhicules en attente de validation', 'value' => $vehicles['en_validation'] ?? 0],
    ];
@endphp

<x-layouts::app :title="$title">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as $card)
            <x-ui.card>
                <p class="text-sm text-neutral-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-semibold text-neutral-900">{{ $card['value'] }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <p class="mt-6 text-sm text-neutral-500">
        Les statistiques de réservation (demandes en attente, réservations à venir, taux d'acceptation)
        seront ajoutées avec le domaine Reservation.
    </p>

    <div class="mt-8 flex gap-3">
        <x-ui.button href="{{ route('partner.residences.index') }}" variant="secondary">Gérer mes résidences</x-ui.button>
        <x-ui.button href="{{ route('partner.vehicles.index') }}" variant="secondary">Gérer mes véhicules</x-ui.button>
        <x-ui.button href="{{ route('partner.availability.index') }}" variant="secondary">Disponibilités</x-ui.button>
    </div>
</x-layouts::app>
