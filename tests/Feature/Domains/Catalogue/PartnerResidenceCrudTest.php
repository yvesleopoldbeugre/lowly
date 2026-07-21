<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Partners\Models\Partner;

it('only lists residences owned by the authenticated partner', function () {
    $partner = Partner::factory()->valide()->create();
    Residence::factory()->create(['partner_id' => $partner->id, 'title' => 'Mine']);
    Residence::factory()->create(['title' => 'Not mine']);

    $response = $this->actingAs($partner->user)->getJson('/api/v1/partner/residences');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    $response->assertJsonPath('data.0.attributes.title', 'Mine');
});

it('creates a draft residence owned by the authenticated partner', function () {
    $partner = Partner::factory()->valide()->create();

    $response = $this->actingAs($partner->user)->postJson('/api/v1/partner/residences', [
        'title' => 'Nouvelle résidence',
        'description' => 'Une belle résidence.',
        'address' => '12 rue des Jardins',
        'city' => 'Abidjan',
        'capacity' => 4,
        'daily_rate' => 55.5,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.status', 'brouillon');

    $residence = Residence::firstOrFail();
    expect($residence->partner_id)->toBe($partner->id);
});

it('allows editing a residence in an editable status', function (?string $factoryState) {
    $partner = Partner::factory()->valide()->create();
    $factory = Residence::factory();
    $residence = ($factoryState ? $factory->{$factoryState}() : $factory)->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)->patchJson("/api/v1/partner/residences/{$residence->id}", [
        'title' => 'Titre modifié',
    ]);

    $response->assertOk();
    expect($residence->fresh()->title)->toBe('Titre modifié');
})->with([
    'brouillon' => null,
    'rejetee' => 'rejetee',
    'publiee' => 'publiee',
]);

it('rejects editing a residence under active admin control', function (string $factoryState) {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->{$factoryState}()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/residences/{$residence->id}", ['title' => 'Titre modifié'])
        ->assertStatus(403);
})->with(['enValidation', 'suspendue']);

it('rejects editing a residence owned by another partner', function () {
    $partner = Partner::factory()->valide()->create();
    $other = Residence::factory()->create();

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/residences/{$other->id}", ['title' => 'Titre modifié'])
        ->assertStatus(403);
});

it('moves a rejected residence back to draft when corrected', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->rejetee()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/residences/{$residence->id}", ['title' => 'Correction'])
        ->assertOk();

    expect($residence->fresh()->status)->toBe('brouillon');
});

it('keeps a published residence published after a routine edit', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/residences/{$residence->id}", ['daily_rate' => 99])
        ->assertOk();

    expect($residence->fresh()->status)->toBe('publiee');
});

it('submits a draft residence for validation when the partner is validated', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/residences/{$residence->id}", ['submit_for_validation' => true])
        ->assertOk();

    expect($residence->fresh()->status)->toBe('en_validation');
});

it('rejects submission for validation when the partner is not validated', function () {
    $partner = Partner::factory()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)
        ->patchJson("/api/v1/partner/residences/{$residence->id}", ['submit_for_validation' => true]);

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'partner_not_validated');
    expect($residence->fresh()->status)->toBe('brouillon');
});
