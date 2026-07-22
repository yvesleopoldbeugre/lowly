<?php

use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\Reservation;

it('lists only the reservations of the authenticated client', function () {
    $client = User::factory()->client()->create();
    $mine = Reservation::factory()->create(['client_id' => $client->id]);
    $notMine = Reservation::factory()->create();

    $response = $this->actingAs($client)->getJson('/api/v1/reservations');

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($mine->id)->not->toContain($notMine->id);
});

it('shows a reservation to its owning client', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->create(['client_id' => $client->id]);

    $this->actingAs($client)->getJson("/api/v1/reservations/{$reservation->id}")->assertOk();
});

it('rejects a client viewing another client\'s reservation', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->create();

    $this->actingAs($client)->getJson("/api/v1/reservations/{$reservation->id}")->assertStatus(403);
});
