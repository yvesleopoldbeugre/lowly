@php
    $listingType = fn ($listing) => $listing instanceof \App\Domains\Catalogue\Models\Vehicle ? 'vehicle' : 'residence';
    $listingTitle = fn ($listing) => $listing instanceof \App\Domains\Catalogue\Models\Vehicle
        ? "{$listing->brand} {$listing->model}"
        : $listing->title;
@endphp

<x-layouts::app :title="$title">
    <p class="mb-4 text-sm text-neutral-500">{{ $listings->count() }} annonce(s) en attente</p>

    <div class="flex flex-col gap-4">
        @forelse ($listings as $listing)
            @php $type = $listingType($listing); @endphp
            <x-ui.card x-data="adminListingReview('{{ $type }}', '{{ $listing->id }}')">
                <div class="flex items-start justify-between">
                    <div>
                        <x-ui.badge variant="neutral">{{ $type === 'vehicle' ? 'Véhicule' : 'Résidence' }}</x-ui.badge>
                        <h2 class="mt-1 text-sm font-semibold text-neutral-900">{{ $listingTitle($listing) }}</h2>
                        <p class="text-xs text-neutral-500">
                            Partenaire : {{ $listing->partner->company_name ?? $listing->partner->user->full_name }} ·
                            Soumise le {{ $listing->updated_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <template x-if="generalError"><p class="mt-3 text-xs text-danger-600" x-text="generalError"></p></template>

                <div class="mt-4 flex gap-2">
                    <x-ui.button x-bind:disabled="loading" @click="validate()">Valider</x-ui.button>
                    <x-ui.button variant="secondary" x-bind:disabled="loading" @click="showRejectForm = !showRejectForm">Rejeter</x-ui.button>
                </div>

                <div x-show="showRejectForm" x-cloak class="mt-4 flex flex-col gap-2 rounded-lg border border-neutral-200 p-4">
                    <x-ui.input label="Motif du rejet (obligatoire)" x-model="reason" />
                    <template x-if="errors.reason"><p class="text-xs text-danger-600" x-text="errors.reason"></p></template>
                    <x-ui.button variant="danger" x-bind:disabled="loading" @click="reject()" class="self-start">Confirmer le rejet</x-ui.button>
                </div>
            </x-ui.card>
        @empty
            <p class="rounded-lg border border-dashed border-neutral-300 p-8 text-center text-sm text-neutral-500">
                Aucune annonce en attente de validation.
            </p>
        @endforelse
    </div>
</x-layouts::app>
