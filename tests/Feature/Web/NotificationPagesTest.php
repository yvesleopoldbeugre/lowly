<?php

use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

it('redirects a guest to login when visiting the notifications page', function () {
    $this->get('/notifications')->assertRedirect(route('login.show'));
});

it('renders the notifications list scoped to the authenticated user', function () {
    $user = User::factory()->create();
    Notification::factory()->create(['user_id' => $user->id, 'type' => 'reservation_confirmee']);
    $other = Notification::factory()->create();

    $response = $this->actingAs($user)->get('/notifications');

    $response->assertOk();
    $response->assertViewIs('pages.shared.notifications.index');
    $response->assertSee('Réservation confirmée');
    $response->assertDontSee($other->id);
});

it('shows the unread notification badge on a client page', function () {
    $client = User::factory()->client()->create();
    Notification::factory()->create(['user_id' => $client->id]);

    $response = $this->actingAs($client)->get('/');

    $response->assertOk();
    $response->assertSee('1');
});

it('shows the unread notification badge on a partner page', function () {
    $partner = Partner::factory()->valide()->create();
    Notification::factory()->create(['user_id' => $partner->user_id]);

    $response = $this->actingAs($partner->user)->get('/partner/dashboard');

    $response->assertOk();
    $response->assertSee('1');
});

it('does not show a badge when there are no unread notifications', function () {
    $client = User::factory()->client()->create();
    Notification::factory()->lue()->create(['user_id' => $client->id]);

    $response = $this->actingAs($client)->get('/');

    $response->assertOk();
    $response->assertDontSee('-right-2 -top-2', false);
});
