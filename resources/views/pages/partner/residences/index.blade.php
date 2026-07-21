@php
    $badgeVariant = [
        'brouillon' => 'neutral',
        'en_validation' => 'warning',
        'publiee' => 'success',
        'rejetee' => 'danger',
        'suspendue' => 'danger',
    ];
@endphp

<x-layouts::app :title="$title">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-neutral-500">{{ $residences->total() }} résidence(s)</p>
        <x-ui.button href="{{ route('partner.residences.create') }}">+ Ajouter une résidence</x-ui.button>
    </div>

    <x-ui.card class="overflow-x-auto p-0">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase text-neutral-400">
                <tr>
                    <th class="px-5 py-3">Titre</th>
                    <th class="px-5 py-3">Ville</th>
                    <th class="px-5 py-3">Statut</th>
                    <th class="px-5 py-3">Tarif</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($residences as $residence)
                    <tr>
                        <td class="px-5 py-3">{{ $residence->title }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $residence->city }}</td>
                        <td class="px-5 py-3">
                            <x-ui.badge :variant="$badgeVariant[$residence->status] ?? 'neutral'">{{ $residence->status }}</x-ui.badge>
                        </td>
                        <td class="px-5 py-3">{{ number_format($residence->daily_rate, 0, ',', ' ') }} FCFA</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('partner.residences.edit', $residence) }}" class="text-primary-700 hover:underline">Éditer</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-neutral-500">Aucune résidence pour l'instant.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.card>

    <div class="mt-6">{{ $residences->links() }}</div>
</x-layouts::app>
