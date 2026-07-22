@php
    $percent = fn (?float $rate) => $rate === null ? '—' : number_format($rate * 100, 1, ',', ' ').' %';

    $cards = [
        ['label' => "Taux d'acceptation des demandes", 'value' => $percent($statistics['reservation_acceptance_rate'])],
        ['label' => 'Délai moyen de réponse partenaire', 'value' => $statistics['average_partner_response_delay_hours'] !== null ? $statistics['average_partner_response_delay_hours'].' h' : '—'],
        ['label' => 'Taux de contre-proposition acceptée', 'value' => $percent($statistics['counter_offer_acceptance_rate'])],
        ['label' => 'Partenaires actifs validés', 'value' => $statistics['active_validated_partners']],
        ['label' => 'Réservations confirmées sans erreur de calendrier', 'value' => $percent($statistics['confirmed_reservations_calendar_accuracy_rate'])],
    ];
@endphp

<x-layouts::app :title="$title">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($cards as $card)
            <x-ui.card>
                <p class="text-sm text-neutral-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-semibold text-neutral-900">{{ $card['value'] }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <p class="mt-6 text-xs text-neutral-500">
        Le taux de conversion recherche→demande (PRODUCT.md §11) n'est pas encore disponible :
        aucun suivi des événements de recherche n'existe dans le domaine Catalogue.
    </p>
</x-layouts::app>
