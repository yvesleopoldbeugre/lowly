<?php

namespace Database\Factories;

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\ResidencePhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResidencePhoto>
 */
class ResidencePhotoFactory extends Factory
{
    protected $model = ResidencePhoto::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'residence_id' => Residence::factory(),
            'path' => 'residences/'.fake()->uuid().'.jpg',
            'position' => fake()->numberBetween(0, 9),
        ];
    }
}
