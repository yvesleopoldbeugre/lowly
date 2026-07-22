<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Identity\Models\User;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;

it('redirects a guest to login when visiting the reservations list', function () {
    $this->get('/reservations')->assertRedirect(route('login.show'));
});

it('rejects a partner from client reservation pages', function () {
    $reservation = Reservation::factory()->create();

    $this->actingAs($reservation->reservable->partner->user)->get('/reservations')->assertStatus(403);
});

it('renders the reservations list scoped to the authenticated client', function () {
    $client = User::factory()->client()->create();
    $mine = Reservation::factory()->create(['client_id' => $client->id]);
    $notMine = Reservation::factory()->create();

    $response = $this->actingAs($client)->get('/reservations');

    $response->assertOk();
    $response->assertSee($mine->reservable->title);
    $response->assertDontSee($notMine->reservable->title);
});

it('shows the reservation detail page to its owning client', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($client)->get("/reservations/{$reservation->id}");

    $response->assertOk();
    $response->assertViewIs('pages.client.reservations.show');
    $response->assertSee($reservation->reservable->title);
});

it('rejects a client viewing another client\'s reservation detail page', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->create();

    $this->actingAs($client)->get("/reservations/{$reservation->id}")->assertStatus(403);
});

it('shows the pending counter-offer on the reservation detail page', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->contreProposee()->create(['client_id' => $client->id]);
    $counterOffer = CounterOffer::factory()->create(['original_reservation_id' => $reservation->id]);

    $response = $this->actingAs($client)->get("/reservations/{$reservation->id}");

    $response->assertOk();
    $response->assertSee('Accepter la proposition');
    $response->assertSee($counterOffer->proposedReservable->title);
});

it('shows the reservation request form on a published listing detail page for a client', function () {
    $client = User::factory()->client()->create();
    $residence = Residence::factory()->publiee()->create();

    $response = $this->actingAs($client)->get("/residences/{$residence->id}");

    $response->assertOk();
    $response->assertSee('Demander à réserver');
    $response->assertSee('reservationRequest', false);
});

it('does not show the reservation request form to a guest', function () {
    $residence = Residence::factory()->publiee()->create();

    $response = $this->get("/residences/{$residence->id}");

    $response->assertOk();
    $response->assertSee('Se connecter pour réserver');
});
