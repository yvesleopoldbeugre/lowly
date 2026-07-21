<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Partners\Models\Partner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads a photo for the authenticated partner\'s residence', function () {
    Storage::fake('public');
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)->postJson(
        "/api/v1/partner/residences/{$residence->id}/photos",
        ['photo' => UploadedFile::fake()->image('photo.jpg'), 'position' => 1],
    );

    $response->assertCreated();

    $photo = $residence->photos()->firstOrFail();
    expect($photo->position)->toBe(1);
    Storage::disk('public')->assertExists($photo->path);
});

it('deletes a photo and its file from disk', function () {
    Storage::fake('public');
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);
    $path = UploadedFile::fake()->image('photo.jpg')->store("residences/{$residence->id}", 'public');
    $photo = $residence->photos()->create(['path' => $path, 'position' => 0]);

    $this->actingAs($partner->user)
        ->deleteJson("/api/v1/partner/residences/{$residence->id}/photos/{$photo->id}")
        ->assertNoContent();

    expect($residence->photos()->find($photo->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('rejects photo management on a residence owned by another partner', function () {
    Storage::fake('public');
    $partner = Partner::factory()->valide()->create();
    $other = Residence::factory()->create();

    $this->actingAs($partner->user)->postJson(
        "/api/v1/partner/residences/{$other->id}/photos",
        ['photo' => UploadedFile::fake()->image('photo.jpg')],
    )->assertStatus(403);
});
