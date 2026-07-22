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

    /**
     * Lit un délai (en heures) depuis une ligne `{"hours": n}` — voir
     * BUSINESS_RULES.md §6.2 (délai de réponse à une contre-proposition,
     * configurable en plateforme, aucune valeur n'étant fixée par les
     * règles métier elles-mêmes). `$default` ne sert que si la ligne
     * n'existe pas en base, jamais comme substitut à une valeur configurée.
     */
    public static function hours(string $key, int $default): int
    {
        return (int) (self::where('key', $key)->first()?->value['hours'] ?? $default);
    }
}
