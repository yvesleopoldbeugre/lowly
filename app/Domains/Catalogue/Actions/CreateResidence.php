<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Partners\Models\Partner;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`POST /api/v1/partner/residences`).
 */
final class CreateResidence
{
    /**
     * @param  array{title: string, description: string, address: string, city: string, capacity: int, daily_rate: float, attributes?: array<string, mixed>}  $data
     */
    public function executer(Partner $partner, array $data): Residence
    {
        return Residence::create([
            ...$data,
            'partner_id' => $partner->id,
            'status' => 'brouillon',
        ]);
    }
}
