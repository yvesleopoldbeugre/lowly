<?php

namespace Database\Factories;

use App\Domains\Administration\Models\AdminAction;
use App\Domains\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminAction>
 */
class AdminActionFactory extends Factory
{
    protected $model = AdminAction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => User::factory()->admin(),
            'action_type' => fake()->randomElement(['validation_partenaire', 'rejet_annonce', 'validation_annonce']),
            'target_type' => 'residence',
            'target_id' => fake()->uuid(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
