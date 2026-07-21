<?php

namespace App\Domains\Administration\Models;

use App\Domains\Identity\Models\User;
use Database\Factories\AdminActionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Domaine Administration — voir DATABASE.md §10.1.
 */
class AdminAction extends Model
{
    /** @use HasFactory<AdminActionFactory> */
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'admin_id',
        'action_type',
        'target_type',
        'target_id',
        'notes',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function target(): MorphTo
    {
        return $this->morphTo(null, 'target_type', 'target_id');
    }

    protected static function newFactory(): AdminActionFactory
    {
        return AdminActionFactory::new();
    }
}
