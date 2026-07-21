<?php

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Models\ReservationStatusHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReservationStatusHistory>
 */
class ReservationStatusHistoryFactory extends Factory
{
    protected $model = ReservationStatusHistory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'previous_status' => null,
            'new_status' => 'en_attente',
            'changed_by' => User::factory()->client(),
            'changed_at' => now(),
        ];
    }
}
