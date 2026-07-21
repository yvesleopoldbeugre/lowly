<?php

namespace Database\Factories;

use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement([
                'nouvelle_demande',
                'reservation_confirmee',
                'reservation_refusee',
                'contre_proposition_recue',
                'contre_proposition_expiree',
                'partenaire_valide',
                'annonce_validee',
            ]),
            'payload' => ['message' => fake()->sentence()],
            'read_at' => null,
        ];
    }

    public function lue(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => now()]);
    }
}
