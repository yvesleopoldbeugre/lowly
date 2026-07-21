<?php

namespace App\Domains\Catalogue\Actions;

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Catalogue\Models\VehiclePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Domaine Catalogue — voir API_GUIDE.md §11 (`POST .../vehicles/{id}/photos`).
 * Même logique de stockage que AddResidencePhoto (disque `public`,
 * renommage UUID).
 */
final class AddVehiclePhoto
{
    public function executer(Vehicle $vehicle, UploadedFile $file, ?int $position): VehiclePhoto
    {
        $filename = Str::uuid()->toString().'.'.$file->extension();
        $path = $file->storeAs("vehicles/{$vehicle->id}", $filename, 'public');

        return $vehicle->photos()->create([
            'path' => $path,
            'position' => $position ?? 0,
        ]);
    }
}
