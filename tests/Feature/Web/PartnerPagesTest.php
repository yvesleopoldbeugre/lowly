<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

it('redirects a guest to login when visiting the partner dashboard', function () {
    $this->get('/partner/dashboard')->assertRedirect(route('login.show'));
});

it('rejects a client from partner pages', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->get('/partner/dashboard')->assertStatus(403);
});

it('renders the partner dashboard with real data', function () {
    $partner = Partner::factory()->valide()->create();
    Residence::factory()->publiee()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)->get('/partner/dashboard');

    $response->assertOk();
    $response->assertViewIs('pages.partner.dashboard');
    $response->assertViewHas('stats.residences.publiee', 1);
});

it('renders the partner residences list scoped to the authenticated partner', function () {
    $partner = Partner::factory()->valide()->create();
    Residence::factory()->create(['partner_id' => $partner->id, 'title' => 'Mine']);
    Residence::factory()->create(['title' => 'Not mine']);

    $response = $this->actingAs($partner->user)->get('/partner/residences');

    $response->assertOk();
    $response->assertSee('Mine');
    $response->assertDontSee('Not mine');
});

it('renders the residence create form', function () {
    $partner = Partner::factory()->valide()->create();

    $this->actingAs($partner->user)->get('/partner/residences/create')->assertOk();
});

it('rejects editing a residence owned by another partner', function () {
    $partner = Partner::factory()->valide()->create();
    $other = Residence::factory()->create();

    $this->actingAs($partner->user)->get("/partner/residences/{$other->id}/edit")->assertStatus(403);
});

it('renders the availability calendar page', function () {
    $partner = Partner::factory()->valide()->create();
    Residence::factory()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)->get('/partner/availability');

    $response->assertOk();
    $response->assertViewIs('pages.partner.availability.index');
});

it('shows an empty-state message on the availability page when the partner has no listings', function () {
    $partner = Partner::factory()->valide()->create();

    $response = $this->actingAs($partner->user)->get('/partner/availability');

    $response->assertOk();
    $response->assertSee('résidence ou un véhicule');
});
