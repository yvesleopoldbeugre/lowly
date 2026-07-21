<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use Illuminate\Support\Str;

it('renders the home page with only published residences', function () {
    Residence::factory()->publiee()->create(['title' => 'Publiée']);
    Residence::factory()->create(['title' => 'Brouillon']);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertViewIs('pages.public.home');
    $response->assertSee('Publiée');
    $response->assertDontSee('Brouillon');
});

it('renders the home page filtered to vehicles', function () {
    Vehicle::factory()->publie()->create(['model' => 'Corolla']);

    $response = $this->get('/?type=vehicle');

    $response->assertOk();
    $response->assertViewHas('type', 'vehicle');
    $response->assertSee('Corolla');
});

it('shows a published residence detail page', function () {
    $residence = Residence::factory()->publiee()->create();

    $response = $this->get("/residences/{$residence->id}");

    $response->assertOk();
    $response->assertViewIs('pages.public.listing-detail');
    $response->assertSee($residence->title);
});

it('returns 404 for a residence that is not published', function () {
    $residence = Residence::factory()->create();

    $this->get("/residences/{$residence->id}")->assertNotFound();
});

it('returns 404 for an unknown residence id', function () {
    $this->get('/residences/'.Str::uuid())->assertNotFound();
});
