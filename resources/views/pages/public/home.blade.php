<x-layouts::guest :title="$title">
    <section class="bg-primary-700 px-4 py-12">
        <div class="mx-auto max-w-4xl">
            <h1 class="mb-6 text-center text-2xl font-semibold text-white sm:text-3xl">
                Trouvez une résidence ou un véhicule en toute confiance
            </h1>

            <form
                method="GET"
                x-data="{ type: '{{ $type }}' }"
                class="rounded-lg bg-white p-4 shadow-sm sm:p-6"
            >
                <div class="mb-4 flex gap-2">
                    <button
                        type="button"
                        @click="type = 'residence'"
                        :class="type === 'residence' ? 'bg-primary-100 text-primary-800' : 'bg-neutral-100 text-neutral-600'"
                        class="rounded-md px-3 py-1.5 text-sm font-medium"
                    >Résidences</button>
                    <button
                        type="button"
                        @click="type = 'vehicle'"
                        :class="type === 'vehicle' ? 'bg-primary-100 text-primary-800' : 'bg-neutral-100 text-neutral-600'"
                        class="rounded-md px-3 py-1.5 text-sm font-medium"
                    >Véhicules</button>
                </div>

                <input type="hidden" name="type" x-model="type">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                    <x-ui.input
                        label="Ville"
                        name="city"
                        value="{{ $filters['city'] }}"
                        placeholder="Abidjan, Cocody…"
                        x-show="type === 'residence'"
                    />
                    <x-ui.input label="Prix min (FCFA)" name="min_price" type="number" value="{{ $filters['min_price'] }}" />
                    <x-ui.input label="Prix max (FCFA)" name="max_price" type="number" value="{{ $filters['max_price'] }}" />
                    <x-ui.input
                        label="Capacité min."
                        name="capacity"
                        type="number"
                        value="{{ $filters['capacity'] }}"
                        x-show="type === 'residence'"
                    />
                </div>

                <x-ui.button type="submit" class="mt-4 w-full justify-center sm:w-auto">Rechercher</x-ui.button>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10">
        <div class="mb-4 flex items-baseline justify-between">
            <h2 class="text-lg font-semibold text-neutral-900">
                {{ $type === 'residence' ? 'Résidences disponibles' : 'Véhicules disponibles' }}
            </h2>
            <p class="text-sm text-neutral-500">{{ $listings->total() }} résultat(s)</p>
        </div>

        @if ($listings->isEmpty())
            <p class="rounded-lg border border-dashed border-neutral-300 p-8 text-center text-sm text-neutral-500">
                Aucun résultat pour cette recherche. Essayez d'élargir vos critères.
            </p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($listings as $listing)
                    <x-domain.listing-card
                        title="{{ $type === 'residence' ? $listing->title : "{$listing->brand} {$listing->model}" }}"
                        subtitle="{{ $type === 'residence' ? $listing->city : $listing->year }}"
                        :daily-rate="$listing->daily_rate"
                        :photo-url="$listing->photos->first()?->path ? asset('storage/'.$listing->photos->first()->path) : null"
                        href="{{ $type === 'residence' ? route('residences.show', $listing) : route('vehicles.show', $listing) }}"
                    />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $listings->appends($filters + ['type' => $type])->links() }}
            </div>
        @endif
    </section>
</x-layouts::guest>
