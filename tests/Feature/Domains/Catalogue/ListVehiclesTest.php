<?php

use App\Domains\Catalogue\Models\Vehicle;

it('only lists published vehicles', function () {
    Vehicle::factory()->publie()->create(['model' => 'Publié']);
    Vehicle::factory()->create(['model' => 'Brouillon']);
    Vehicle::factory()->enValidation()->create(['model' => 'En validation']);
    Vehicle::factory()->rejete()->create(['model' => 'Rejeté']);
    Vehicle::factory()->suspendu()->create(['model' => 'Suspendu']);

    $response = $this->getJson('/api/v1/vehicles');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.model', 'Publié');
});

it('filters vehicles by price range', function () {
    Vehicle::factory()->publie()->create(['daily_rate' => 20]);
    Vehicle::factory()->publie()->create(['daily_rate' => 60]);
    Vehicle::factory()->publie()->create(['daily_rate' => 140]);

    $response = $this->getJson('/api/v1/vehicles?min_price=30&max_price=100');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.daily_rate', '60.00');
});

it('paginates vehicles with the documented envelope', function () {
    Vehicle::factory()->publie()->count(3)->create();

    $response = $this->getJson('/api/v1/vehicles?per_page=2');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
    $response->assertJsonPath('meta.total', 3);
    $response->assertJsonPath('meta.last_page', 2);
});
