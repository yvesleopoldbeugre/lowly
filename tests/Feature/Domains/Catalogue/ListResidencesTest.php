<?php

use App\Domains\Catalogue\Models\Residence;

it('only lists published residences', function () {
    Residence::factory()->publiee()->create(['title' => 'Publiée']);
    Residence::factory()->create(['title' => 'Brouillon']);
    Residence::factory()->enValidation()->create(['title' => 'En validation']);
    Residence::factory()->rejetee()->create(['title' => 'Rejetée']);
    Residence::factory()->suspendue()->create(['title' => 'Suspendue']);

    $response = $this->getJson('/api/v1/residences');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.title', 'Publiée');
});

it('filters residences by city', function () {
    Residence::factory()->publiee()->create(['city' => 'Abidjan']);
    Residence::factory()->publiee()->create(['city' => 'Bouaké']);

    $response = $this->getJson('/api/v1/residences?city=Abidjan');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.city', 'Abidjan');
});

it('filters residences by price range', function () {
    Residence::factory()->publiee()->create(['daily_rate' => 30]);
    Residence::factory()->publiee()->create(['daily_rate' => 90]);
    Residence::factory()->publiee()->create(['daily_rate' => 200]);

    $response = $this->getJson('/api/v1/residences?min_price=50&max_price=150');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.daily_rate', '90.00');
});

it('filters residences by minimum capacity', function () {
    Residence::factory()->publiee()->create(['capacity' => 2]);
    Residence::factory()->publiee()->create(['capacity' => 6]);

    $response = $this->getJson('/api/v1/residences?capacity=4');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.capacity', 6);
});

it('paginates residences with the documented envelope', function () {
    Residence::factory()->publiee()->count(3)->create();

    $response = $this->getJson('/api/v1/residences?per_page=2');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
    $response->assertJsonPath('meta.current_page', 1);
    $response->assertJsonPath('meta.per_page', 2);
    $response->assertJsonPath('meta.total', 3);
    $response->assertJsonPath('meta.last_page', 2);
});

it('caps per_page at 100', function () {
    Residence::factory()->publiee()->count(2)->create();

    $response = $this->getJson('/api/v1/residences?per_page=500');

    $response->assertOk();
    $response->assertJsonPath('meta.per_page', 100);
});
