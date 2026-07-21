<?php

namespace App\Domains\Administration\Models;

use Database\Factories\PlatformSettingFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Domaine Administration — voir DATABASE.md §10.2. Seule `updated_at` existe
 * en base (pas de `created_at`).
 */
class PlatformSetting extends Model
{
    /** @use HasFactory<PlatformSettingFactory> */
    use HasFactory, HasUuids;

    const CREATED_AT = null;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    protected static function newFactory(): PlatformSettingFactory
    {
        return PlatformSettingFactory::new();
    }
}
