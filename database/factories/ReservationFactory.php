<?php

namespace Database\Factories;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Convention de journée 12h-12h — voir BUSINESS_RULES.md §3.1-§3.3 :
        // la borne haute de la période (date de départ) n'est jamais facturée.
        $start = fake()->dateTimeBetween('+2 days', '+25 days');
        $nights = fake()->numberBetween(1, 6);
        $end = (clone $start)->modify("+{$nights} days");
        $dailyRate = fake()->randomFloat(2, 20, 300);

        return [
            'client_id' => User::factory()->client(),
            'reservable_type' => 'residence',
            'reservable_id' => Residence::factory()->publiee(),
            'period' => ['start' => $start, 'end' => $end],
            'nights_count' => $nights,
            'total_amount' => round($dailyRate * $nights, 2),
            'status' => 'en_attente',
            'parent_reservation_id' => null,
        ];
    }

    /**
     * Bascule la réservation sur un véhicule plutôt qu'une résidence.
     */
    public function forVehicle(): static
    {
        return $this->state(fn (array $attributes) => [
            'reservable_type' => 'vehicle',
            'reservable_id' => Vehicle::factory()->publie(),
        ]);
    }

    public function confirmee(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'confirmee']);
    }

    public function refusee(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'refusee']);
    }

    public function contreProposee(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'contre_proposee']);
    }

    public function expiree(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'expiree']);
    }
}
