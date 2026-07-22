<?php

use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Models\ReservationStatusHistory;

it('exposes the parent/children relationship between an original and a resulting reservation', function () {
    $original = Reservation::factory()->refusee()->create();
    $resulting = Reservation::factory()->confirmee()->create(['parent_reservation_id' => $original->id]);

    expect($resulting->parent->id)->toBe($original->id)
        ->and($original->children->pluck('id'))->toContain($resulting->id)
        ->and($resulting->isConfirmed())->toBeTrue()
        ->and($original->isConfirmed())->toBeFalse();
});

it('links a status history entry to the user who triggered the change', function () {
    $reservation = Reservation::factory()->create();
    $history = ReservationStatusHistory::factory()->create(['reservation_id' => $reservation->id]);

    expect($history->changer)->not->toBeNull()
        ->and($history->reservation->id)->toBe($reservation->id)
        ->and($reservation->statusHistory->pluck('id'))->toContain($history->id);
});
