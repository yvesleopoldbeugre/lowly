<?php

use App\Domains\Identity\Models\User;

it('renders the login page', function () {
    $this->get('/login')->assertOk()->assertViewIs('pages.public.auth');
});

it('renders the register page', function () {
    $this->get('/register')->assertOk()->assertViewIs('pages.public.auth');
});

it('redirects an authenticated user away from the login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/login')->assertRedirect();
});

it('redirects a guest to login when visiting the profile page', function () {
    $this->get('/me')->assertRedirect(route('login.show'));
});

it('renders the profile page with the authenticated user data', function () {
    $user = User::factory()->create(['full_name' => 'Amara Koné']);

    $response = $this->actingAs($user)->get('/me');

    $response->assertOk();
    $response->assertViewIs('pages.client.profile');
    $response->assertSee('Amara Koné');
});
