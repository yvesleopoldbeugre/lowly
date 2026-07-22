@php
    $isResidence = $type === 'residence';
    $subtitle = $isResidence
        ? "{$listing->city} · {$listing->capacity} personnes max."
        : "{$listing->brand} {$listing->model} · {$listing->year}";
@endphp

<x-layouts::guest :title="$title">
    <div class="mx-auto max-w-6xl px-4 py-8">
        @if ($listing->photos->isNotEmpty())
            <div class="mb-6 grid grid-cols-4 grid-rows-2 gap-2" style="height: 360px;">
                <img
                    src="{{ asset('storage/'.$listing->photos->first()->path) }}"
                    alt="Photo principale de {{ $title }}"
                    class="col-span-2 row-span-2 h-full w-full rounded-lg object-cover"
                >
                @foreach ($listing->photos->skip(1)->take(4) as $photo)
                    <img
                        src="{{ asset('storage/'.$photo->path) }}"
                        alt="Photo secondaire de {{ $title }}"
                        class="h-full w-full rounded-lg object-cover"
                    >
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-ui.badge variant="success">{{ $isResidence ? 'Publiée' : 'Publié' }}</x-ui.badge>
                <h1 class="mt-3 text-2xl font-semibold text-neutral-900">{{ $title }}</h1>
                <p class="mt-1 text-sm text-neutral-500">{{ $subtitle }}</p>

                @if ($isResidence)
                    <p class="mt-4 whitespace-pre-line text-sm text-neutral-700">{{ $listing->description }}</p>
                @endif

                @if (!empty($listing->attributes))
                    <div class="mt-6">
                        <h2 class="mb-2 text-sm font-semibold text-neutral-900">Équipements et caractéristiques</h2>
                        <div class="grid grid-cols-2 gap-2 text-sm text-neutral-600 sm:grid-cols-3">
                            @foreach ($listing->attributes as $label => $value)
                                <span>✓ {{ Str::headline($label) }}@if (! is_bool($value)) : {{ $value }} @endif</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="rounded-lg border border-neutral-200 p-5">
                <p class="text-lg font-semibold text-neutral-900">
                    {{ number_format($listing->daily_rate, 0, ',', ' ') }} FCFA / jour
                </p>

                @auth
                    @if (auth()->user()->isClient())
                        <div x-data="reservationRequest('{{ $type }}', '{{ $listing->id }}', {{ $listing->daily_rate }})">
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <x-ui.input label="Arrivée" type="date" x-model="start_date" />
                                <x-ui.input label="Départ" type="date" x-model="end_date" />
                            </div>

                            <template x-if="nightsCount > 0">
                                <p class="mt-2 text-xs text-neutral-500">
                                    <span x-text="nightsCount"></span> journée(s) — <span x-text="totalAmount.toLocaleString('fr-FR')"></span> FCFA (estimation)
                                </p>
                            </template>

                            <template x-if="errors.start_date || errors.end_date"><p class="mt-2 text-xs text-danger-600">Période invalide.</p></template>
                            <template x-if="generalError"><p class="mt-2 text-xs text-danger-600" x-text="generalError"></p></template>

                            <x-ui.button x-bind:disabled="loading || nightsCount < 1" @click="submit()" class="mt-4 w-full justify-center">
                                Demander à réserver
                            </x-ui.button>
                            <p class="mt-2 text-xs text-neutral-500">
                                Aucun prélèvement à cette étape — validation du partenaire requise.
                            </p>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-neutral-500">La réservation est réservée aux comptes clients.</p>
                    @endif
                @else
                    <x-ui.button href="{{ route('login.show') }}" class="mt-4 w-full justify-center">Se connecter pour réserver</x-ui.button>
                @endauth
            </aside>
        </div>
    </div>
</x-layouts::guest>
