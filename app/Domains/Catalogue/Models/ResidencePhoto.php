<?php

namespace App\Domains\Catalogue\Models;

use Database\Factories\ResidencePhotoFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Domaine Catalogue — voir DATABASE.md §6.2. Seule `created_at` existe en base.
 */
class ResidencePhoto extends Model
{
    /** @use HasFactory<ResidencePhotoFactory> */
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'residence_id',
        'path',
        'position',
    ];

    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }

    protected static function newFactory(): ResidencePhotoFactory
    {
        return ResidencePhotoFactory::new();
    }
}
