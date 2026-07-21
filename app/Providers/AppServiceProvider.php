<?php

namespace App\Providers;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use Illuminate\Database\Eloquent\Relations\Relation;
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
    }
}
