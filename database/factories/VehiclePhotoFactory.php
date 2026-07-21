<?php

namespace Database\Factories;

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Catalogue\Models\VehiclePhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehiclePhoto>
 */
class VehiclePhotoFactory extends Factory
{
    protected $model = VehiclePhoto::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'path' => 'vehicles/'.fake()->uuid().'.jpg',
            'position' => fake()->numberBetween(0, 9),
        ];
    }
}
