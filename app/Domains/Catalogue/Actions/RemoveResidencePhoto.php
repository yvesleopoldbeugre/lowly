<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\ResidencePhoto;
use Illuminate\Support\Facades\Storage;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`DELETE .../residences/{id}/photos/{photoId}`).
 */
final class RemoveResidencePhoto
{
    public function executer(ResidencePhoto $photo): void
    {
        Storage::disk('public')->delete($photo->path);

        $photo->delete();
    }
}
