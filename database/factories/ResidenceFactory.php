<?php

namespace Database\Factories;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Partners\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Residence>
 */
class ResidenceFactory extends Factory
{
    protected $model = Residence::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory()->valide(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'capacity' => fake()->numberBetween(1, 8),
            'daily_rate' => fake()->randomFloat(2, 20, 300),
            'attributes' => [
                'wifi' => fake()->boolean(),
                'climatisation' => fake()->boolean(),
                'nombre_chambres' => fake()->numberBetween(1, 4),
            ],
            'status' => 'brouillon',
        ];
    }

    public function enValidation(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'en_validation']);
    }

    public function publiee(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'publiee']);
    }

    public function rejetee(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'rejetee']);
    }

    public function suspendue(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'suspendue']);
    }
}
