@php
    $weekdayOffset = $blockable ? $month->copy()->startOfMonth()->dayOfWeekIso - 1 : 0;
    $selectedType = $blockable instanceof \App\Domains\Catalogue\Models\Vehicle ? 'vehicle' : 'residence';
    $selectedValue = $blockable ? $selectedType.':'.$blockable->id : '';
@endphp

<x-layouts::app :title="$title">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <select
            onchange="window.location.href = '{{ route('partner.availability.index') }}?blockable_type=' + this.value.split(':')[0] + '&blockable_id=' + this.value.split(':')[1]"
            class="rounded-md border border-neutral-300 px-3 py-2 text-sm"
        >
            @foreach ($residences as $residence)
                <option value="residence:{{ $residence->id }}" @selected($selectedValue === 'residence:'.$residence->id)>
                    {{ $residence->title }} — résidence
                </option>
            @endforeach
            @foreach ($vehicles as $vehicle)
                <option value="vehicle:{{ $vehicle->id }}" @selected($selectedValue === 'vehicle:'.$vehicle->id)>
                    {{ $vehicle->brand }} {{ $vehicle->model }} — véhicule
                </option>
            @endforeach
        </select>
    </div>

    @if (!$blockable)
        <p class="rounded-lg border border-dashed border-neutral-300 p-8 text-center text-sm text-neutral-500">
            Créez d'abord une résidence ou un véhicule pour gérer ses disponibilités.
        </p>
    @else
        <div x-data="availabilityCalendar('{{ $selectedType }}', '{{ $blockable->id }}')">
            <div class="mb-4 flex items-center justify-between">
                <a
                    href="{{ route('partner.availability.index', ['blockable_type' => $selectedType, 'blockable_id' => $blockable->id, 'month' => $month->copy()->subMonth()->month, 'year' => $month->copy()->subMonth()->year]) }}"
                    class="rounded-md px-3 py-1.5 text-sm text-neutral-600 hover:bg-neutral-100"
                >←</a>
                <p class="text-sm font-medium text-neutral-900">{{ $month->translatedFormat('F Y') }}</p>
                <a
                    href="{{ route('partner.availability.index', ['blockable_type' => $selectedType, 'blockable_id' => $blockable->id, 'month' => $month->copy()->addMonth()->month, 'year' => $month->copy()->addMonth()->year]) }}"
                    class="rounded-md px-3 py-1.5 text-sm text-neutral-600 hover:bg-neutral-100"
                >→</a>
                <x-ui.button @click="showModal = true">+ Ajouter un blocage manuel</x-ui.button>
            </div>

            <div class="mb-4 flex gap-4 text-xs text-neutral-600">
                <span class="flex items-center gap-1"><span class="h-3 w-3 rounded border border-neutral-300 bg-white"></span> Disponible</span>
                <span class="flex items-center gap-1"><span class="h-3 w-3 rounded border border-danger-600 bg-danger-100"></span> Réservation confirmée</span>
                <span class="flex items-center gap-1"><span class="h-3 w-3 rounded border border-neutral-400 bg-neutral-200"></span> Blocage manuel</span>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-neutral-500">
                <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
            </div>
            <div class="mt-1 grid grid-cols-7 gap-1">
                @for ($i = 0; $i < $weekdayOffset; $i++)
                    <div></div>
                @endfor
                @foreach ($days as $day)
                    <div
                        class="flex h-14 flex-col items-start rounded-md border p-1 text-xs
                            @if ($day['status'] === 'reserved') border-danger-600 bg-danger-100 text-danger-800
                            @elseif ($day['status'] === 'manual') border-neutral-400 bg-neutral-200 text-neutral-700
                            @else border-neutral-200 bg-white text-neutral-600 @endif"
                    >
                        <span>{{ $day['date']->day }}</span>
                        @if ($day['label'])
                            <span class="text-[10px]">{{ $day['label'] }}</span>
                            <button type="button" @click="deleteBlock('{{ $day['block']->id }}')" class="mt-auto text-[10px] text-danger-700 underline">Retirer</button>
                        @elseif ($day['status'] === 'reserved')
                            <span class="text-[10px]">Réservé</span>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Modale blocage manuel --}}
            <div x-show="showModal" x-cloak class="fixed inset-0 z-10 flex items-center justify-center bg-black/30 p-4">
                <div @click.outside="showModal = false" class="w-full max-w-sm rounded-lg bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold text-neutral-900">Ajouter un blocage manuel</h2>

                    <div class="flex flex-col gap-3">
                        <label class="block">
                            <span class="mb-1 block text-xs font-medium text-neutral-500">Motif</span>
                            <select x-model="origin" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm">
                                <option value="entretien">Entretien</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="usage_personnel">Usage personnel</option>
                                <option value="autre">Autre indisponibilité</option>
                            </select>
                        </label>

                        <div class="grid grid-cols-2 gap-3">
                            <x-ui.input label="Du" type="date" x-model="start_date" />
                            <x-ui.input label="Au" type="date" x-model="end_date" />
                        </div>

                        <template x-if="errors.start_date || errors.end_date"><p class="text-xs text-danger-600">Période invalide.</p></template>
                        <template x-if="generalError"><p class="text-xs text-danger-600" x-text="generalError"></p></template>

                        <div class="mt-2 flex justify-end gap-2">
                            <x-ui.button variant="secondary" @click="showModal = false">Annuler</x-ui.button>
                            <x-ui.button x-bind:disabled="loading" @click="submitBlock()">Bloquer la période</x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-layouts::app>
