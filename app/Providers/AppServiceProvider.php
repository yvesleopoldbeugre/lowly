<?php

namespace App\Providers;

use App\Domains\Availability\Listeners\BloquerCalendrier;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Communication\Listeners\NotifierConfirmation;
use App\Domains\Communication\Listeners\NotifierContrePropositionRecue;
use App\Domains\Communication\Listeners\NotifierExpiration;
use App\Domains\Communication\Listeners\NotifierNouvelleDemande;
use App\Domains\Communication\Listeners\NotifierRefus;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Events\ContrePropositionExpiree;
use App\Domains\Reservation\Events\ContrePropositionSoumise;
use App\Domains\Reservation\Events\DemandeReservationCreee;
use App\Domains\Reservation\Events\ReservationConfirmee;
use App\Domains\Reservation\Events\ReservationRefusee;
use App\Domains\Reservation\Listeners\EnregistrerHistorique;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fige les valeurs stockées dans les colonnes polymorphes
        // (reservable_type, blockable_type, proposed_reservable_type, target_type)
        // sur les identifiants courts documentés dans DATABASE.md, plutôt que sur
        // le nom de classe PHP complet — voir DATABASE.md §7.2, §8.1, §8.3, §10.1.
        // `user` et `partner` couvrent les valeurs possibles de `admin_actions.target_type`.
        Relation::enforceMorphMap([
            'residence' => Residence::class,
            'vehicle' => Vehicle::class,
            'user' => User::class,
            'partner' => Partner::class,
        ]);

        // Protection anti brute-force sur l'authentification — voir
        // docs/engineering/10-security-guidelines.md §6 (5 tentatives/minute/IP).
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        // Permet <x-layouts.guest>/<x-layouts.app> : resources/views/layouts/
        // reste un répertoire distinct de components/ (arborescence imposée
        // par docs/engineering/06-blade-tailwind-guidelines.md §4), mais
        // utilisable avec la même syntaxe de composant anonyme.
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        // Communication inter-domaines exclusivement par événements — voir
        // ARCHITECTURE.md §9. Les listeners vivent dans le domaine qui
        // réagit (pas d'auto-discovery depuis app/Listeners), enregistrés
        // ici explicitement.
        Event::listen(DemandeReservationCreee::class, NotifierNouvelleDemande::class);

        Event::listen(ReservationConfirmee::class, [EnregistrerHistorique::class, 'confirmee']);
        Event::listen(ReservationConfirmee::class, BloquerCalendrier::class);
        Event::listen(ReservationConfirmee::class, NotifierConfirmation::class);

        Event::listen(ReservationRefusee::class, [EnregistrerHistorique::class, 'refusee']);
        Event::listen(ReservationRefusee::class, NotifierRefus::class);

        Event::listen(ContrePropositionSoumise::class, [EnregistrerHistorique::class, 'contrePropositionSoumise']);
        Event::listen(ContrePropositionSoumise::class, NotifierContrePropositionRecue::class);

        Event::listen(ContrePropositionExpiree::class, [EnregistrerHistorique::class, 'contrePropositionExpiree']);
        Event::listen(ContrePropositionExpiree::class, NotifierExpiration::class);
    }
}
