<?php

namespace Database\Factories;

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailabilityBlock>
 */
class AvailabilityBlockFactory extends Factory
{
    protected $model = AvailabilityBlock::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+2 days', '+25 days');
        $end = (clone $start)->modify('+'.fake()->numberBetween(1, 6).' days');

        return [
            'blockable_type' => 'residence',
            'blockable_id' => Residence::factory()->publiee(),
            'period' => ['start' => $start, 'end' => $end],
            'origin' => 'reservation',
            'reservation_id' => null,
            'created_by' => User::factory()->partner(),
        ];
    }

    /**
     * Blocage manuel (entretien, maintenance, usage personnel) — voir
     * BUSINESS_RULES.md §4.2, applicable aux véhicules.
     */
    public function manuel(string $motif = 'entretien'): static
    {
        return $this->state(fn (array $attributes) => [
            'blockable_type' => 'vehicle',
            'origin' => $motif,
            'reservation_id' => null,
        ]);
    }
}
