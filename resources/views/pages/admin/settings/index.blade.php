@php
    $labels = [
        'reservation_response_delay_hours' => 'Délai de réponse partenaire (h)',
        'counter_offer_response_delay_hours' => "Délai d'expiration contre-proposition (h)",
    ];
@endphp

<x-layouts::app :title="$title">
    <div class="flex max-w-md flex-col gap-4">
        @foreach ($settings as $setting)
            <x-ui.card x-data="adminSettingsForm('{{ $setting->key }}', {{ $setting->value['hours'] ?? 0 }})">
                <x-ui.input :label="$labels[$setting->key] ?? $setting->key" type="number" min="1" x-model="hours" />

                <template x-if="generalError"><p class="mt-2 text-xs text-danger-600" x-text="generalError"></p></template>
                <template x-if="saved"><p class="mt-2 text-xs text-success-700">Enregistré.</p></template>

                <x-ui.button x-bind:disabled="loading" @click="save()" class="mt-3">Enregistrer</x-ui.button>
            </x-ui.card>
        @endforeach
    </div>
</x-layouts::app>
