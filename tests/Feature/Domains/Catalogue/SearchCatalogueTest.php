<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;

it('requires the type parameter', function () {
    $response = $this->getJson('/api/v1/search?city=Abidjan');

    $response->assertStatus(422);
    $response->assertJsonPath('error.code', 'validation_failed');
});

it('rejects an invalid type value', function () {
    $response = $this->getJson('/api/v1/search?type=hotel');

    $response->assertStatus(422);
});

it('searches published residences when type=residence', function () {
    Residence::factory()->publiee()->create(['city' => 'Abidjan']);
    Residence::factory()->publiee()->create(['city' => 'Bouaké']);
    Vehicle::factory()->publie()->create();

    $response = $this->getJson('/api/v1/search?type=residence&city=Abidjan');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.type', 'residence');
    $response->assertJsonPath('data.0.attributes.city', 'Abidjan');
});

it('searches published vehicles when type=vehicle', function () {
    Vehicle::factory()->publie()->create(['daily_rate' => 60]);
    Vehicle::factory()->publie()->create(['daily_rate' => 140]);
    Residence::factory()->publiee()->create();

    $response = $this->getJson('/api/v1/search?type=vehicle&max_price=100');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.type', 'vehicle');
});

it('rejects start_date and end_date', function () {
    $response = $this->getJson('/api/v1/search?type=residence&start_date=2026-01-10&end_date=2026-01-13');

    $response->assertStatus(422);
    $response->assertJsonPath('error.code', 'validation_failed');
});
