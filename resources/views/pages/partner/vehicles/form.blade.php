@php
    $initial = $vehicle ? [
        'brand' => $vehicle->brand,
        'model' => $vehicle->model,
        'year' => $vehicle->year,
        'plate_number' => $vehicle->plate_number,
        'daily_rate' => $vehicle->daily_rate,
        'attributes' => $vehicle->attributes,
    ] : [];
@endphp

<x-layouts::app :title="$title">
    <div
        x-data="vehicleForm({{ Illuminate\Support\Js::from($vehicle?->id) }}, {{ Illuminate\Support\Js::from($initial) }})"
        class="grid grid-cols-1 gap-6 lg:grid-cols-3"
    >
        <x-ui.card class="lg:col-span-2">
            <form @submit.prevent class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.input label="Marque" x-model="brand" />
                    <x-ui.input label="Modèle" x-model="model" />
                </div>
                <template x-if="errors.brand"><p class="-mt-3 text-xs text-danger-600" x-text="errors.brand"></p></template>

                <div class="grid grid-cols-2 gap-4">
                    <x-ui.input label="Année" type="number" x-model="year" />
                    <x-ui.input label="Immatriculation" x-model="plate_number" />
                </div>

                <x-ui.input label="Tarif journalier (FCFA)" type="number" min="0" step="0.01" x-model="daily_rate" />
                <template x-if="errors.daily_rate"><p class="-mt-3 text-xs text-danger-600" x-text="errors.daily_rate"></p></template>

                <div class="grid grid-cols-2 gap-4">
                    <label class="block">
                        <span class="mb-1 block text-xs font-medium text-neutral-500">Boîte</span>
                        <select x-model="attributes.boite" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm">
                            <option value="manuelle">Manuelle</option>
                            <option value="automatique">Automatique</option>
                        </select>
                    </label>
                    <x-ui.input label="Places" type="number" min="1" x-model="attributes.places" />
                </div>
                <label class="flex items-center gap-2 text-sm text-neutral-700">
                    <input type="checkbox" x-model="attributes.climatisation"> Climatisation
                </label>

                <template x-if="generalError"><p class="text-sm text-danger-600" x-text="generalError"></p></template>
                <template x-if="savedMessage"><p class="text-sm text-success-700" x-text="savedMessage"></p></template>

                <div class="flex gap-3">
                    <x-ui.button variant="secondary" x-bind:disabled="loading" @click="submit(false)">
                        Enregistrer en brouillon
                    </x-ui.button>
                    <x-ui.button x-bind:disabled="loading" @click="submit(true)">
                        Soumettre pour validation
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        @if ($vehicle)
            <x-ui.card
                x-data="photoManager({{ Illuminate\Support\Js::from($vehicle->photos->map(fn ($photo) => ['id' => $photo->id, 'attributes' => ['path' => $photo->path]])) }}, '/api/v1/partner/vehicles/{{ $vehicle->id }}/photos')"
            >
                <h2 class="mb-3 text-sm font-semibold text-neutral-900">Photos</h2>

                <div class="mb-3 grid grid-cols-3 gap-2">
                    <template x-for="photo in photos" :key="photo.id">
                        <div class="group relative">
                            <img :src="'/storage/' + photo.attributes.path" class="h-20 w-full rounded object-cover" alt="Photo">
                            <button
                                type="button"
                                @click="remove(photo.id)"
                                class="absolute right-1 top-1 hidden h-5 w-5 items-center justify-center rounded-full bg-danger-600 text-xs text-white group-hover:flex"
                            >×</button>
                        </div>
                    </template>
                </div>

                <label class="block text-sm text-primary-700">
                    <span x-show="!uploading">+ Ajouter une photo</span>
                    <span x-show="uploading">Envoi…</span>
                    <input type="file" accept="image/*" class="hidden" @change="upload">
                </label>
                <template x-if="error"><p class="mt-2 text-xs text-danger-600" x-text="error"></p></template>
            </x-ui.card>
        @endif
    </div>
</x-layouts::app>
