<?php

namespace Database\Factories;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CounterOffer>
 */
class CounterOfferFactory extends Factory
{
    protected $model = CounterOffer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+2 days', '+25 days');
        $end = (clone $start)->modify('+'.fake()->numberBetween(1, 6).' days');

        return [
            'original_reservation_id' => Reservation::factory()->refusee(),
            'proposed_reservable_type' => 'residence',
            'proposed_reservable_id' => Residence::factory()->publiee(),
            'proposed_period' => ['start' => $start, 'end' => $end],
            'status' => 'en_attente',
            // Délai de réponse configurable côté plateforme — voir
            // BUSINESS_RULES.md §6.2 ; 72h par défaut dans les données de démo.
            'expires_at' => now()->addHours(72),
        ];
    }

    public function acceptee(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'acceptee']);
    }

    public function refusee(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'refusee']);
    }

    public function expiree(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expiree',
            'expires_at' => now()->subDay(),
        ]);
    }
}
