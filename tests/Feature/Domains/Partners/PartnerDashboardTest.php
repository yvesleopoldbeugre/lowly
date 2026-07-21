<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Partners\Models\Partner;

it('returns a status breakdown limited to the authenticated partner\'s own listings', function () {
    $partner = Partner::factory()->valide()->create();
    Residence::factory()->create(['partner_id' => $partner->id]);
    Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    Vehicle::factory()->publie()->create(['partner_id' => $partner->id]);

    // Biens d'un autre partenaire — ne doivent pas être comptés.
    Residence::factory()->publiee()->create();
    Vehicle::factory()->publie()->create();

    $response = $this->actingAs($partner->user)->getJson('/api/v1/partner/dashboard');

    $response->assertOk();
    $response->assertJsonPath('data.attributes.residences.brouillon', 1);
    $response->assertJsonPath('data.attributes.residences.publiee', 1);
    $response->assertJsonPath('data.attributes.vehicles.publie', 1);
});

it('rejects unauthenticated access to the dashboard', function () {
    $this->getJson('/api/v1/partner/dashboard')->assertStatus(401);
});
