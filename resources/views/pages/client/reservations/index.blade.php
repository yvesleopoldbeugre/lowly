@php
    $badgeVariant = [
        'en_attente' => 'warning',
        'confirmee' => 'success',
        'refusee' => 'danger',
        'contre_proposee' => 'warning',
        'expiree' => 'neutral',
    ];
@endphp

<x-layouts::guest :title="$title">
    <div class="mx-auto max-w-4xl px-4 py-8">
        <h1 class="mb-6 text-2xl font-semibold text-neutral-900">Mes réservations</h1>

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
                                <a href="{{ route('reservations.show', $reservation) }}" class="text-primary-700 hover:underline">Détail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-6 text-center text-neutral-500">Aucune réservation pour l'instant.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.card>

        <div class="mt-6">{{ $reservations->links() }}</div>
    </div>
</x-layouts::guest>
