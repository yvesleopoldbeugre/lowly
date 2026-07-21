<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Exceptions\PartnerNotValidatedException;
use App\Domains\Catalogue\Models\Vehicle;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`PATCH /api/v1/partner/vehicles/{id}`).
 *
 * Même machine à états que UpdateResidence (voir UML.md §9), adaptée aux
 * valeurs de statut véhicule (`rejete`/`publie`).
 */
final class UpdateVehicle
{
    /**
     * @param  array{brand?: string, model?: string, year?: ?int, plate_number?: ?string, daily_rate?: float, attributes?: array<string, mixed>, submit_for_validation?: bool}  $data
     */
    public function executer(Vehicle $vehicle, array $data): Vehicle
    {
        $submitForValidation = $data['submit_for_validation'] ?? false;
        unset($data['submit_for_validation']);

        $vehicle->fill($data);

        if ($vehicle->status === 'rejete') {
            $vehicle->status = 'brouillon';
        }

        if ($submitForValidation && $vehicle->status === 'brouillon') {
            if (! $vehicle->partner->isValidated()) {
                throw new PartnerNotValidatedException;
            }

            $vehicle->status = 'en_validation';
        }

        $vehicle->save();

        return $vehicle;
    }
}
