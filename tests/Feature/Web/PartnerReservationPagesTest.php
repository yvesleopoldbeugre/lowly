<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\Reservation;

it('redirects a guest to login when visiting the partner reservations list', function () {
    $this->get('/partner/reservations')->assertRedirect(route('login.show'));
});

it('rejects a client from partner reservation pages', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->get('/partner/reservations')->assertStatus(403);
});

it('renders the partner reservations list scoped to their own listings', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $mine = Reservation::factory()->create(['reservable_type' => 'residence', 'reservable_id' => $residence->id]);
    $notMine = Reservation::factory()->create();

    $response = $this->actingAs($partner->user)->get('/partner/reservations');

    $response->assertOk();
    $response->assertSee($mine->reservable->title);
    $response->assertDontSee($notMine->reservable->title);
});

it('shows the reservation detail page with accept/reject/counter-offer actions for a pending request', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $reservation = Reservation::factory()->create(['reservable_type' => 'residence', 'reservable_id' => $residence->id]);

    $response = $this->actingAs($partner->user)->get("/partner/reservations/{$reservation->id}");

    $response->assertOk();
    $response->assertViewIs('pages.partner.reservations.show');
    $response->assertSee('Accepter');
    $response->assertSee('Refuser avec contre-proposition');
});

it('rejects a partner viewing a reservation for a listing they do not own', function () {
    $partner = Partner::factory()->valide()->create();
    $reservation = Reservation::factory()->create();

    $this->actingAs($partner->user)->get("/partner/reservations/{$reservation->id}")->assertStatus(403);
});
