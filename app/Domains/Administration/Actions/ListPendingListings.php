<?php

namespace App\Domains\Administration\Actions;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use Illuminate\Support\Collection;

/**
 * Domaine Administration — voir API_GUIDE.md §12 (`GET /admin/listings/pending`).
 * Combine résidences et véhicules (deux modèles distincts, pas d'union
 * native) en une file unique triée par date de soumission ; non paginée,
 * la file d'attente de validation restant naturellement de faible volume
 * (voir docs/ux/mockups/07-validation-admin.html, simples compteurs).
 */
final class ListPendingListings
{
    /**
     * @return Collection<int, Residence|Vehicle>
     */
    public function executer(): Collection
    {
        return Residence::query()->where('status', 'en_validation')->get()
            ->concat(Vehicle::query()->where('status', 'en_validation')->get())
            ->sortBy('created_at')
            ->values();
    }
}
