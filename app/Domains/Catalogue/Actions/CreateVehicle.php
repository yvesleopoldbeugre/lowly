<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Partners\Models\Partner;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`POST /api/v1/partner/vehicles`).
 */
final class CreateVehicle
{
    /**
     * @param  array{brand: string, model: string, year?: ?int, plate_number?: ?string, daily_rate: float, attributes?: array<string, mixed>}  $data
     */
    public function executer(Partner $partner, array $data): Vehicle
    {
        return Vehicle::create([
            ...$data,
            'partner_id' => $partner->id,
            'status' => 'brouillon',
        ]);
    }
}
