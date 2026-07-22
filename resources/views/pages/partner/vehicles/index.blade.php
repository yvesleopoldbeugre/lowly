@php
    $badgeVariant = [
        'brouillon' => 'neutral',
        'en_validation' => 'warning',
        'publie' => 'success',
        'rejete' => 'danger',
        'suspendu' => 'danger',
    ];
@endphp

<x-layouts::app :title="$title">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-neutral-500">{{ $vehicles->total() }} véhicule(s)</p>
        <x-ui.button href="{{ route('partner.vehicles.create') }}">+ Ajouter un véhicule</x-ui.button>
    </div>

    <x-ui.card class="overflow-x-auto p-0">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase text-neutral-400">
                <tr>
                    <th class="px-5 py-3">Véhicule</th>
                    <th class="px-5 py-3">Statut</th>
                    <th class="px-5 py-3">Tarif</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($vehicles as $vehicle)
                    <tr>
                        <td class="px-5 py-3">{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                        <td class="px-5 py-3">
                            <x-ui.badge :variant="$badgeVariant[$vehicle->status] ?? 'neutral'">{{ $vehicle->status }}</x-ui.badge>
                        </td>
                        <td class="px-5 py-3">{{ number_format($vehicle->daily_rate, 0, ',', ' ') }} FCFA</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('partner.vehicles.edit', $vehicle) }}" class="text-primary-700 hover:underline">Éditer</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-6 text-center text-neutral-500">Aucun véhicule pour l'instant.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>

    <div class="mt-6">{{ $vehicles->links() }}</div>
</x-layouts::app>
