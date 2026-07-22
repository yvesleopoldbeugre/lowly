<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LOWLY' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-neutral-50 font-sans text-neutral-900">
    <header class="border-b border-neutral-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ url('/') }}" class="text-xl font-semibold text-primary-700">LOWLY</a>

            <nav class="hidden items-center gap-6 text-sm text-neutral-600 md:flex">
                <a href="{{ url('/?type=residence') }}" class="hover:text-neutral-900">Résidences</a>
                <a href="{{ url('/?type=vehicle') }}" class="hover:text-neutral-900">Véhicules</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    @if (auth()->user()->isClient())
                        <a href="{{ route('reservations.index') }}" class="text-sm text-neutral-600 hover:text-neutral-900">Mes réservations</a>
                    @endif
                    <a href="{{ route('me.show') }}" class="text-sm font-medium text-neutral-700 hover:text-neutral-900">
                        {{ auth()->user()->full_name }}
                    </a>
                    <x-ui.button variant="ghost" onclick="window.lowlyLogout()">Déconnexion</x-ui.button>
                @else
                    <x-ui.button variant="ghost" href="{{ route('login.show') }}">Connexion</x-ui.button>
                    <x-ui.button href="{{ route('register.show') }}">S'inscrire</x-ui.button>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-neutral-200 py-6 text-center text-xs text-neutral-400">
        LOWLY — marketplace de mise en relation, jamais un exploitant. Voir PRODUCT.md §2-3.
    </footer>
</body>
</html>
