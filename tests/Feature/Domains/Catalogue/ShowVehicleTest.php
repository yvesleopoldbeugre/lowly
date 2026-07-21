<?php

use App\Domains\Catalogue\Models\Vehicle;
use Illuminate\Support\Str;

it('shows a published vehicle with its photos', function () {
    $vehicle = Vehicle::factory()->publie()->create();
    $vehicle->photos()->create(['path' => 'vehicles/photo-1.jpg', 'position' => 0]);

    $response = $this->getJson("/api/v1/vehicles/{$vehicle->id}");

    $response->assertOk();
    $response->assertJsonPath('data.id', $vehicle->id);
    $response->assertJsonPath('data.photos.0.attributes.path', 'vehicles/photo-1.jpg');
});

it('returns 404 for a vehicle that is not published', function () {
    $vehicle = Vehicle::factory()->enValidation()->create();

    $this->getJson("/api/v1/vehicles/{$vehicle->id}")->assertNotFound();
});

it('returns 404 for an unknown vehicle id', function () {
    $this->getJson('/api/v1/vehicles/'.Str::uuid())->assertNotFound();
});
