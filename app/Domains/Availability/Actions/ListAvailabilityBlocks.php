<?php

namespace App\Domains\Availability\Actions;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Domaine Availability — alimente le calendrier partenaire (page Web), voir
 * UX_UI.md §6.3 et docs/ux/mockups/06-disponibilites.html.
 *
 * Aucune Action de lecture n'existait encore dans ce domaine (seules
 * store/destroy avaient été construites pour l'API) — nécessaire ici pour
 * injecter les blocages du mois au chargement de la page, conformément à
 * docs/engineering/07-javascript-guidelines.md §5 ("jamais par un appel
 * Ajax séparé bloquant l'affichage initial").
 */
final class ListAvailabilityBlocks
{
    public function executer(Residence|Vehicle $blockable, int $month, int $year): Collection
    {
        $monthStart = Carbon::create($year, $month, 1)->toDateString();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth()->addDay()->toDateString();

        return $blockable->availabilityBlocks()
            ->whereRaw('period && daterange(?, ?)', [$monthStart, $monthEnd])
            ->get();
    }
}
