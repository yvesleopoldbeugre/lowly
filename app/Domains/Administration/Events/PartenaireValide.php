<?php

namespace App\Domains\Administration\Events;

use App\Domains\Partners\Models\Partner;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Domaine Administration — voir ARCHITECTURE.md §8.3/§9.
 */
final class PartenaireValide
{
    use Dispatchable;

    public function __construct(public readonly Partner $partner)
    {
        //
    }
}
