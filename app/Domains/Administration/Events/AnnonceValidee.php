<?php

namespace App\Domains\Administration\Events;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Domaine Administration — voir ARCHITECTURE.md §9.
 */
final class AnnonceValidee
{
    use Dispatchable;

    public function __construct(public readonly Residence|Vehicle $listing)
    {
        //
    }
}
