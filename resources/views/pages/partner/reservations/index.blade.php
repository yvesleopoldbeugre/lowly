@php
    $badgeVariant = [
        'en_attente' => 'warning',
        'confirmee' => 'success',
        'refusee' => 'danger',
        'contre_proposee' => 'warning',
        'expiree' => 'neutral',
    ];
@endphp

<x-layouts::app :title="$title">
    <p class="mb-4 text-sm text-neutral-500">{{ $reservations->total() }} demande(s)/réservation(s)</p>

    <x-ui.card class="overflow-x-auto p-0">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase text-neutral-400">
                <tr>
                    <th class="px-5 py-3">Bien</th>
                    <th class="px-5 py-3">Dates</th>
                    <th class="px-5 py-3">Statut</th>
                    <th class="px-5 py-3">Montant</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($reservations as $reservation)
                    <tr>
                        <td class="px-5 py-3">
                            {{ $reservation->reservable instanceof \App\Domains\Catalogue\Models\Vehicle
                                ? "{$reservation->reservable->brand} {$reservation->reservable->model}"
                                : $reservation->reservable->title }}
                        </td>
                        <td class="px-5 py-3 text-neutral-500">
                            {{ $reservation->period['start']->format('d/m/Y') }} → {{ $reservation->period['end']->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <x-ui.badge :variant="$badgeVariant[$reservation->status] ?? 'neutral'">{{ $reservation->status }}</x-ui.badge>
                        </td>
                        <td class="px-5 py-3">{{ number_format($reservation->total_amount, 0, ',', ' ') }} FCFA</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('partner.reservations.show', $reservation) }}" class="text-primary-700 hover:underline">Détail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-neutral-500">Aucune demande reçue pour l'instant.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>

    <div class="mt-6">{{ $reservations->links() }}</div>
</x-layouts::app>
