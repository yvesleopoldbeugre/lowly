<?php

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;

it('accepts a counter-offer and confirms a new reservation with the alternative listing blocked', function () {
    $partner = Partner::factory()->valide()->create();
    $client = User::factory()->client()->create();
    $original = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $alternative = Residence::factory()->publiee()->create(['partner_id' => $partner->id, 'daily_rate' => 50]);

    $reservation = Reservation::factory()->contreProposee()->create([
        'client_id' => $client->id,
        'reservable_type' => 'residence',
        'reservable_id' => $original->id,
    ]);
    $counterOffer = CounterOffer::factory()->create([
        'original_reservation_id' => $reservation->id,
        'proposed_reservable_type' => 'residence',
        'proposed_reservable_id' => $alternative->id,
        'proposed_period' => ['start' => '2026-09-01', 'end' => '2026-09-04'],
        'status' => 'en_attente',
        'expires_at' => now()->addDay(),
    ]);

    $response = $this->actingAs($client)->postJson("/api/v1/reservations/{$reservation->id}/counter-offers/{$counterOffer->id}/accept");

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.status', 'confirmee');
    $response->assertJsonPath('data.attributes.total_amount', '150.00');
    $response->assertJsonPath('data.relationships.parent_reservation.id', $reservation->id);

    expect($counterOffer->fresh()->status)->toBe('acceptee');

    $newReservation = Reservation::where('parent_reservation_id', $reservation->id)->firstOrFail();
    expect(AvailabilityBlock::where('reservation_id', $newReservation->id)->exists())->toBeTrue();
});

it('rejects a counter-offer and closes the original reservation cycle', function () {
    $partner = Partner::factory()->valide()->create();
    $client = User::factory()->client()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);

    $reservation = Reservation::factory()->contreProposee()->create([
        'client_id' => $client->id,
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
    ]);
    $counterOffer = CounterOffer::factory()->create([
        'original_reservation_id' => $reservation->id,
        'status' => 'en_attente',
        'expires_at' => now()->addDay(),
    ]);

    $response = $this->actingAs($client)->postJson("/api/v1/reservations/{$reservation->id}/counter-offers/{$counterOffer->id}/reject");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'refusee');
    expect($counterOffer->fresh()->status)->toBe('refusee');
});

it('rejects responding to an expired counter-offer', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->contreProposee()->create(['client_id' => $client->id]);
    $counterOffer = CounterOffer::factory()->create([
        'original_reservation_id' => $reservation->id,
        'status' => 'en_attente',
        'expires_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($client)->postJson("/api/v1/reservations/{$reservation->id}/counter-offers/{$counterOffer->id}/accept");

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'counter_offer_expired');
});

it('rejects responding to an already-answered counter-offer', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->contreProposee()->create(['client_id' => $client->id]);
    $counterOffer = CounterOffer::factory()->acceptee()->create([
        'original_reservation_id' => $reservation->id,
    ]);

    $response = $this->actingAs($client)->postJson("/api/v1/reservations/{$reservation->id}/counter-offers/{$counterOffer->id}/reject");

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'counter_offer_already_answered');
});

it('rejects rejecting an expired counter-offer', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->contreProposee()->create(['client_id' => $client->id]);
    $counterOffer = CounterOffer::factory()->create([
        'original_reservation_id' => $reservation->id,
        'status' => 'en_attente',
        'expires_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($client)->postJson("/api/v1/reservations/{$reservation->id}/counter-offers/{$counterOffer->id}/reject");

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'counter_offer_expired');
});

it('rejects accepting an already-answered counter-offer', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->contreProposee()->create(['client_id' => $client->id]);
    $counterOffer = CounterOffer::factory()->refusee()->create([
        'original_reservation_id' => $reservation->id,
    ]);

    $response = $this->actingAs($client)->postJson("/api/v1/reservations/{$reservation->id}/counter-offers/{$counterOffer->id}/accept");

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'counter_offer_already_answered');
});

it('rejects a client responding to another client\'s counter-offer', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->contreProposee()->create();
    $counterOffer = CounterOffer::factory()->create([
        'original_reservation_id' => $reservation->id,
        'status' => 'en_attente',
        'expires_at' => now()->addDay(),
    ]);

    $this->actingAs($client)
        ->postJson("/api/v1/reservations/{$reservation->id}/counter-offers/{$counterOffer->id}/accept")
        ->assertStatus(403);
});

it('exposes the pending counter-offer when viewing the reservation', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->contreProposee()->create(['client_id' => $client->id]);
    CounterOffer::factory()->create(['original_reservation_id' => $reservation->id]);

    $response = $this->actingAs($client)->getJson("/api/v1/reservations/{$reservation->id}");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'contre_proposee');
    expect($response->json('data.relationships.counter_offer'))->not->toBeNull();
});
