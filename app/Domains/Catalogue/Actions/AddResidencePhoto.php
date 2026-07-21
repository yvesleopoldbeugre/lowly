<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\ResidencePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`POST .../residences/{id}/photos`).
 *
 * Stocke sur le disque `public` (photos d'annonces servables publiquement,
 * voir ARCHITECTURE.md §11 — à distinguer des documents justificatifs
 * partenaires, sur disque restreint). Renommage UUID à l'upload, voir
 * docs/engineering/10-security-guidelines.md §8.
 */
final class AddResidencePhoto
{
    public function executer(Residence $residence, UploadedFile $file, ?int $position): ResidencePhoto
    {
        $filename = Str::uuid()->toString().'.'.$file->extension();
        $path = $file->storeAs("residences/{$residence->id}", $filename, 'public');

        return $residence->photos()->create([
            'path' => $path,
            'position' => $position ?? 0,
        ]);
    }
}
