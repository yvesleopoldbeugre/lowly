<?php

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->partner(),
            'company_name' => fake()->optional()->company(),
            'legal_document_path' => null,
            'status' => 'en_attente',
            'validated_at' => null,
            'validated_by' => null,
        ];
    }

    public function valide(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'valide',
            'validated_at' => now(),
            'validated_by' => User::factory()->admin(),
        ]);
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
