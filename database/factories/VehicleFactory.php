<?php

namespace Database\Factories;

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Partners\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory()->valide(),
            'brand' => fake()->randomElement(['Toyota', 'Renault', 'Hyundai', 'Kia', 'Peugeot']),
            'model' => fake()->word(),
            'year' => fake()->numberBetween(2015, 2026),
            'plate_number' => strtoupper(fake()->bothify('??-###-??')),
            'daily_rate' => fake()->randomFloat(2, 15, 150),
            'attributes' => [
                'boite' => fake()->randomElement(['manuelle', 'automatique']),
                'places' => fake()->numberBetween(2, 9),
                'climatisation' => fake()->boolean(),
            ],
            'status' => 'brouillon',
        ];
    }

    public function enValidation(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'en_validation']);
    }

    public function publie(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'publie']);
    }

    public function rejete(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'rejete']);
    }

    public function suspendu(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'suspendu']);
    }
}
