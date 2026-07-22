<x-layouts::app :title="$title">
    <p class="mb-4 text-sm text-neutral-500">{{ $partners->total() }} partenaire(s) en attente</p>

    <div class="flex flex-col gap-4">
        @forelse ($partners as $partner)
            <x-ui.card x-data="adminPartnerReview('{{ $partner->id }}')">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-neutral-900">
                            {{ $partner->company_name ?? $partner->user->full_name }}
                        </h2>
                        <p class="text-xs text-neutral-500">
                            Inscrit le {{ $partner->created_at->format('d/m/Y') }} ·
                            {{ $partner->legal_document_path ? 'Document légal fourni' : 'Document légal manquant' }}
                        </p>
                    </div>
                </div>

                <template x-if="generalError"><p class="mt-3 text-xs text-danger-600" x-text="generalError"></p></template>

                <div class="mt-4 flex gap-2">
                    <x-ui.button x-bind:disabled="loading" @click="validate()">Valider</x-ui.button>
                    <x-ui.button variant="secondary" x-bind:disabled="loading" @click="showRejectForm = !showRejectForm">Rejeter</x-ui.button>
                </div>

                <div x-show="showRejectForm" x-cloak class="mt-4 flex flex-col gap-2 rounded-lg border border-neutral-200 p-4">
                    <x-ui.input label="Notes (optionnel)" x-model="notes" />
                    <x-ui.button variant="danger" x-bind:disabled="loading" @click="reject()" class="self-start">Confirmer le rejet</x-ui.button>
                </div>
            </x-ui.card>
        @empty
            <p class="rounded-lg border border-dashed border-neutral-300 p-8 text-center text-sm text-neutral-500">
                Aucun partenaire en attente de validation.
            </p>
        @endforelse
    </div>

    <div class="mt-6">{{ $partners->links() }}</div>
</x-layouts::app>
