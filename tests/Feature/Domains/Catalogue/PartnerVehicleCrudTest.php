<?php

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Partners\Models\Partner;

it('only lists vehicles owned by the authenticated partner', function () {
    $partner = Partner::factory()->valide()->create();
    Vehicle::factory()->create(['partner_id' => $partner->id, 'model' => 'Mine']);
    Vehicle::factory()->create(['model' => 'Not mine']);

    $response = $this->actingAs($partner->user)->getJson('/api/v1/partner/vehicles');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.model', 'Mine');
});

it('creates a draft vehicle owned by the authenticated partner', function () {
    $partner = Partner::factory()->valide()->create();

    $response = $this->actingAs($partner->user)->postJson('/api/v1/partner/vehicles', [
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'daily_rate' => 40,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.status', 'brouillon');

    $vehicle = Vehicle::firstOrFail();
    expect($vehicle->partner_id)->toBe($partner->id);
});

it('rejects editing a vehicle under active admin control', function (string $factoryState) {
    $partner = Partner::factory()->valide()->create();
    $vehicle = Vehicle::factory()->{$factoryState}()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/vehicles/{$vehicle->id}", ['model' => 'Modifié'])
        ->assertStatus(403);
})->with(['enValidation', 'suspendu']);

it('rejects editing a vehicle owned by another partner', function () {
    $partner = Partner::factory()->valide()->create();
    $other = Vehicle::factory()->create();

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/vehicles/{$other->id}", ['model' => 'Modifié'])
        ->assertStatus(403);
});

it('moves a rejected vehicle back to draft when corrected', function () {
    $partner = Partner::factory()->valide()->create();
    $vehicle = Vehicle::factory()->rejete()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/vehicles/{$vehicle->id}", ['model' => 'Correction'])
        ->assertOk();

    expect($vehicle->fresh()->status)->toBe('brouillon');
});

it('submits a draft vehicle for validation when the partner is validated', function () {
    $partner = Partner::factory()->valide()->create();
    $vehicle = Vehicle::factory()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/vehicles/{$vehicle->id}", ['submit_for_validation' => true])
        ->assertOk();

    expect($vehicle->fresh()->status)->toBe('en_validation');
});

it('rejects submission for validation when the partner is not validated', function () {
    $partner = Partner::factory()->create();
    $vehicle = Vehicle::factory()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/vehicles/{$vehicle->id}", ['submit_for_validation' => true]);

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'partner_not_validated');
});
