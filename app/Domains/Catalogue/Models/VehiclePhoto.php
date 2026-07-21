<?php

namespace App\Domains\Catalogue\Models;

use Database\Factories\VehiclePhotoFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Domaine Catalogue — voir DATABASE.md §6.4. Structure identique à ResidencePhoto.
 */
class VehiclePhoto extends Model
{
    /** @use HasFactory<VehiclePhotoFactory> */
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'vehicle_id',
        'path',
        'position',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    protected static function newFactory(): VehiclePhotoFactory
    {
        return VehiclePhotoFactory::new();
    }
}
