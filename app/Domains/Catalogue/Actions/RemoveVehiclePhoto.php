<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\VehiclePhoto;
use Illuminate\Support\Facades\Storage;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`DELETE .../vehicles/{id}/photos/{photoId}`).
 */
final class RemoveVehiclePhoto
{
    public function executer(VehiclePhoto $photo): void
    {
        Storage::disk('public')->delete($photo->path);

        $photo->delete();
    }
}
