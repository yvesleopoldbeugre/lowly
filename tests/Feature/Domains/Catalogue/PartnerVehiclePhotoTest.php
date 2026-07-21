<?php

use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Partners\Models\Partner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads a photo for the authenticated partner\'s vehicle', function () {
    Storage::fake('public');
    $partner = Partner::factory()->valide()->create();
    $vehicle = Vehicle::factory()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)->postJson(
        "/api/v1/partner/vehicles/{$vehicle->id}/photos",
        ['photo' => UploadedFile::fake()->image('photo.jpg'), 'position' => 2],
    );

    $response->assertCreated();

    $photo = $vehicle->photos()->firstOrFail();
    expect($photo->position)->toBe(2);
    Storage::disk('public')->assertExists($photo->path);
});

it('deletes a vehicle photo and its file from disk', function () {
    Storage::fake('public');
    $partner = Partner::factory()->valide()->create();
    $vehicle = Vehicle::factory()->create(['partner_id' => $partner->id]);
    $path = UploadedFile::fake()->image('photo.jpg')->store("vehicles/{$vehicle->id}", 'public');
    $photo = $vehicle->photos()->create(['path' => $path, 'position' => 0]);

    $this->actingAs($partner->user)
        ->deleteJson("/api/v1/partner/vehicles/{$vehicle->id}/photos/{$photo->id}")
        ->assertNoContent();

    expect($vehicle->photos()->find($photo->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('rejects photo management on a vehicle owned by another partner', function () {
    Storage::fake('public');
    $partner = Partner::factory()->valide()->create();
    $other = Vehicle::factory()->create();

    $this->actingAs($partner->user)->postJson(
        "/api/v1/partner/vehicles/{$other->id}/photos",
        ['photo' => UploadedFile::fake()->image('photo.jpg')],
    )->assertStatus(403);
});
