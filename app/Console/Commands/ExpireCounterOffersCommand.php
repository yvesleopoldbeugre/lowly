<?php

namespace App\Console\Commands;

use App\Domains\Reservation\Actions\ExpirerContrePropositionsAction;
use Illuminate\Console\Command;

/**
 * Domaine Reservation — voir BUSINESS_RULES.md §6.2, planifiée toutes les
 * 15 minutes dans bootstrap/app.php (withSchedule).
 */
class ExpireCounterOffersCommand extends Command
{
    protected $signature = 'reservations:expire-counter-offers';

    protected $description = 'Fait expirer les contre-propositions non traitées dans le délai configuré';

    public function handle(ExpirerContrePropositionsAction $action): int
    {
        $count = $action->executer();

        $this->info("{$count} contre-proposition(s) expirée(s).");

        return self::SUCCESS;
    }
}
