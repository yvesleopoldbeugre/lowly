<?php

use App\Domains\Catalogue\Models\Residence;
use Illuminate\Support\Str;

it('shows a published residence with its photos', function () {
    $residence = Residence::factory()->publiee()->create();
    $residence->photos()->create(['path' => 'residences/photo-1.jpg', 'position' => 0]);

    $response = $this->getJson("/api/v1/residences/{$residence->id}");

    $response->assertOk();
    $response->assertJsonPath('data.id', $residence->id);
    $response->assertJsonPath('data.photos.0.attributes.path', 'residences/photo-1.jpg');
});

it('returns 404 for a residence that is not published', function () {
    $residence = Residence::factory()->enValidation()->create();

    $this->getJson("/api/v1/residences/{$residence->id}")->assertNotFound();
});

it('returns 404 for an unknown residence id', function () {
    $this->getJson('/api/v1/residences/'.Str::uuid())->assertNotFound();
});
