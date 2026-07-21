@php
    $initial = $residence ? [
        'title' => $residence->title,
        'description' => $residence->description,
        'address' => $residence->address,
        'city' => $residence->city,
        'capacity' => $residence->capacity,
        'daily_rate' => $residence->daily_rate,
        'attributes' => $residence->attributes,
    ] : [];
@endphp

<x-layouts::app :title="$title">
    <div
        x-data="residenceForm({{ Illuminate\Support\Js::from($residence?->id) }}, {{ Illuminate\Support\Js::from($initial) }})"
        class="grid grid-cols-1 gap-6 lg:grid-cols-3"
    >
        <x-ui.card class="lg:col-span-2">
            <form @submit.prevent class="flex flex-col gap-4">
                <x-ui.input label="Titre" x-model="title" />
                <template x-if="errors.title"><p class="-mt-3 text-xs text-danger-600" x-text="errors.title"></p></template>

                <label class="block">
                    <span class="mb-1 block text-xs font-medium text-neutral-500">Description</span>
                    <textarea x-model="description" rows="4" class="w-full rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"></textarea>
                </label>
                <template x-if="errors.description"><p class="-mt-3 text-xs text-danger-600" x-text="errors.description"></p></template>

                <x-ui.input label="Adresse" x-model="address" />
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.input label="Ville" x-model="city" />
                    <x-ui.input label="Capacité" type="number" min="1" x-model="capacity" />
                </div>
                <x-ui.input label="Tarif journalier (FCFA)" type="number" min="0" step="0.01" x-model="daily_rate" />
                <template x-if="errors.daily_rate"><p class="-mt-3 text-xs text-danger-600" x-text="errors.daily_rate"></p></template>

                <div>
                    <span class="mb-2 block text-xs font-medium text-neutral-500">Équipements</span>
                    <div class="grid grid-cols-2 gap-2 text-sm text-neutral-700 sm:grid-cols-3">
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="attributes.wifi"> Wifi</label>
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="attributes.climatisation"> Climatisation</label>
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="attributes.parking"> Parking</label>
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="attributes.cuisine_equipee"> Cuisine équipée</label>
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="attributes.machine_a_laver"> Machine à laver</label>
                        <label class="flex items-center gap-2"><input type="checkbox" x-model="attributes.television"> Télévision</label>
                    </div>
                </div>

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

        @if ($residence)
            <x-ui.card
                x-data="photoManager({{ Illuminate\Support\Js::from($residence->photos->map(fn ($photo) => ['id' => $photo->id, 'attributes' => ['path' => $photo->path]])) }}, '/api/v1/partner/residences/{{ $residence->id }}/photos')"
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
