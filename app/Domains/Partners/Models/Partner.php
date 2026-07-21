<?php

namespace App\Domains\Partners\Models;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use Database\Factories\PartnerFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Domaine Partners — voir DATABASE.md §5.1 et UML.md §4.3.
 */
class Partner extends Model
{
    /** @use HasFactory<PartnerFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'company_name',
        'legal_document_path',
        'status',
        'validated_at',
        'validated_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'validated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function residences(): HasMany
    {
        return $this->hasMany(Residence::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function isValidated(): bool
    {
        return $this->status === 'valide';
    }

    protected static function newFactory(): PartnerFactory
    {
        return PartnerFactory::new();
    }
}
