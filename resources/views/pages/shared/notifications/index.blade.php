@php
    $labels = [
        'nouvelle_demande' => 'Nouvelle demande de réservation',
        'reservation_confirmee' => 'Réservation confirmée',
        'reservation_refusee' => 'Réservation refusée',
        'contre_proposition_recue' => 'Contre-proposition reçue',
        'contre_proposition_expiree' => 'Contre-proposition expirée',
        'partenaire_valide' => 'Compte partenaire validé',
        'annonce_validee' => 'Annonce validée',
    ];

    $reservationRoute = auth()->user()->isPartner() ? 'partner.reservations.show' : 'reservations.show';
@endphp

<x-layouts::guest :title="$title">
    <div class="mx-auto max-w-2xl px-4 py-8">
        <h1 class="mb-6 text-2xl font-semibold text-neutral-900">Notifications</h1>

        <x-ui.card class="divide-y divide-neutral-100 p-0">
            @forelse ($notifications as $notification)
                <div
                    x-data="notificationRead('{{ $notification->id }}', {{ $notification->isRead() ? 'true' : 'false' }})"
                    class="flex items-start gap-3 px-5 py-4"
                    x-bind:class="read ? 'bg-white' : 'bg-primary-50'"
                >
                    <span class="mt-1 text-xs" x-text="read ? '○' : '●'"></span>

                    <div class="flex-1">
                        <p class="text-sm text-neutral-900">{{ $labels[$notification->type] ?? $notification->type }}</p>
                        <p class="text-xs text-neutral-500">{{ $notification->created_at->diffForHumans() }}</p>

                        @if (isset($notification->payload['reservation_id']))
                            <a href="{{ route($reservationRoute, $notification->payload['reservation_id']) }}" class="text-xs text-primary-700 hover:underline">
                                Voir la réservation
                            </a>
                        @endif
                    </div>

                    <button type="button" x-show="!read" @click="markRead()" class="text-xs text-primary-700 hover:underline">
                        Marquer comme lue
                    </button>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-sm text-neutral-500">Aucune notification pour l'instant.</p>
            @endforelse
        </x-ui.card>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-layouts::guest>
