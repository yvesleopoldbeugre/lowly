<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LOWLY' }} — {{ auth()->user()->isAdmin() ? 'Espace administration' : 'Espace partenaire' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen bg-neutral-50 font-sans text-neutral-900">
    <aside class="hidden w-56 flex-col border-r border-neutral-200 bg-white p-4 md:flex">
        <a href="{{ url('/') }}" class="mb-6 block text-xl font-semibold text-primary-700">LOWLY</a>

        <nav class="flex flex-col gap-1 text-sm">
            @php
                $navItems = auth()->user()->isAdmin()
                    ? [
                        ['route' => 'admin.partners.index', 'label' => 'Partenaires'],
                        ['route' => 'admin.listings.index', 'label' => 'Annonces'],
                        ['route' => 'admin.users.index', 'label' => 'Utilisateurs'],
                        ['route' => 'admin.statistics.index', 'label' => 'Statistiques'],
                        ['route' => 'admin.settings.index', 'label' => 'Paramètres'],
                    ]
                    : [
                        ['route' => 'partner.dashboard', 'label' => 'Tableau de bord'],
                        ['route' => 'partner.residences.index', 'label' => 'Résidences'],
                        ['route' => 'partner.vehicles.index', 'label' => 'Véhicules'],
                        ['route' => 'partner.availability.index', 'label' => 'Disponibilités'],
                        ['route' => 'partner.reservations.index', 'label' => 'Réservations'],
                    ];
            @endphp

            @foreach ($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="rounded-md px-3 py-2 {{ request()->routeIs($item['route'].'*') ? 'bg-primary-50 text-primary-700' : 'text-neutral-600 hover:bg-neutral-100' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </aside>

    <div class="flex flex-1 flex-col">
        <header class="flex items-center justify-between border-b border-neutral-200 bg-white px-6 py-4">
            <h1 class="text-lg font-semibold text-neutral-900">{{ $title ?? '' }}</h1>

            <div class="flex items-center gap-3">
                <span class="text-sm text-neutral-600">{{ auth()->user()->full_name }}</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-100 text-sm font-medium text-primary-700">
                    {{ Str::substr(auth()->user()->full_name, 0, 1) }}
                </span>
                <x-ui.button variant="ghost" onclick="window.lowlyLogout()">Déconnexion</x-ui.button>
            </div>
        </header>

        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
