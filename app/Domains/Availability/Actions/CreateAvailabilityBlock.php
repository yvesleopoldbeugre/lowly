<?php

namespace App\Domains\Availability\Actions;

use App\Domains\Availability\Exceptions\AvailabilityBlockOverlapException;
use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use Illuminate\Database\QueryException;

/**
 * Domaine Availability — voir API_GUIDE.md §11 (`POST /api/v1/partner/availability-blocks`).
 *
 * La non-duplication de période est garantie par la contrainte d'exclusion
 * GiST `excl_availability_no_overlap` (DATABASE.md §7.2), pas par une
 * vérification applicative préalable — cette Action se contente de
 * traduire l'exception PostgreSQL (SQLSTATE 23P01) en erreur API propre.
 */
final class CreateAvailabilityBlock
{
    /**
     * @param  array{start_date: string, end_date: string, origin: string}  $data
     */
    public function executer(Residence|Vehicle $blockable, User $creator, array $data): AvailabilityBlock
    {
        try {
            return $blockable->availabilityBlocks()->create([
                'period' => ['start' => $data['start_date'], 'end' => $data['end_date']],
                'origin' => $data['origin'],
                'created_by' => $creator->id,
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() === '23P01') {
                throw new AvailabilityBlockOverlapException;
            }

            throw $e;
        }
    }
}
