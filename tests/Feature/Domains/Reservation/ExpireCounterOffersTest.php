<?php

use App\Domains\Communication\Models\Notification;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Models\ReservationStatusHistory;

it('expires pending counter-offers past their delay and closes the original reservation', function () {
    $reservation = Reservation::factory()->contreProposee()->create();
    $counterOffer = CounterOffer::factory()->create([
        'original_reservation_id' => $reservation->id,
        'status' => 'en_attente',
        'expires_at' => now()->subHour(),
    ]);

    $this->artisan('reservations:expire-counter-offers')->assertSuccessful();

    expect($counterOffer->fresh()->status)->toBe('expiree')
        ->and($reservation->fresh()->status)->toBe('expiree');

    expect(ReservationStatusHistory::where('reservation_id', $reservation->id)->where('new_status', 'expiree')->where('changed_by', null)->exists())->toBeTrue();

    expect(Notification::where('user_id', $reservation->client_id)->where('type', 'contre_proposition_expiree')->exists())->toBeTrue();
    expect(Notification::where('user_id', $reservation->reservable->partner->user_id)->where('type', 'contre_proposition_expiree')->exists())->toBeTrue();
});

it('does not touch a counter-offer that has not yet expired', function () {
    $reservation = Reservation::factory()->contreProposee()->create();
    $counterOffer = CounterOffer::factory()->create([
        'original_reservation_id' => $reservation->id,
        'status' => 'en_attente',
        'expires_at' => now()->addHour(),
    ]);

    $this->artisan('reservations:expire-counter-offers')->assertSuccessful();

    expect($counterOffer->fresh()->status)->toBe('en_attente')
        ->and($reservation->fresh()->status)->toBe('contre_proposee');
});
