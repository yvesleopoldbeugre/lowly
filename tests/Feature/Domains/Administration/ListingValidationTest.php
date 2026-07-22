<?php

use App\Domains\Administration\Models\AdminAction;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

it('validates a pending residence and notifies its partner', function () {
    $admin = User::factory()->admin()->create();
    $partner = Partner::factory()->create();
    $residence = Residence::factory()->enValidation()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/listings/residence/{$residence->id}/validate");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'publiee');

    expect(AdminAction::where('target_type', 'residence')->where('target_id', $residence->id)->where('action_type', 'validation_annonce')->exists())->toBeTrue();
    expect(Notification::where('user_id', $partner->user_id)->where('type', 'annonce_validee')->exists())->toBeTrue();
});

it('validates a pending vehicle using its own published status spelling', function () {
    $admin = User::factory()->admin()->create();
    $vehicle = Vehicle::factory()->enValidation()->create();

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/listings/vehicle/{$vehicle->id}/validate");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'publie');
});

it('makes a validated residence appear in public search (validation annonce — TESTING.md §7)', function () {
    $admin = User::factory()->admin()->create();
    $residence = Residence::factory()->enValidation()->create(['title' => 'Villa Validée']);

    $this->get('/')->assertDontSee('Villa Validée');

    $this->actingAs($admin)->postJson("/api/v1/admin/listings/residence/{$residence->id}/validate")->assertOk();

    $this->get('/')->assertSee('Villa Validée');
});

it('rejects a pending residence with a mandatory reason', function () {
    $admin = User::factory()->admin()->create();
    $residence = Residence::factory()->enValidation()->create();

    $this->actingAs($admin)->postJson("/api/v1/admin/listings/residence/{$residence->id}/reject", [])
        ->assertStatus(422);

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/listings/residence/{$residence->id}/reject", [
        'reason' => 'Photos de mauvaise qualité.',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'rejetee');

    $action = AdminAction::where('target_id', $residence->id)->where('action_type', 'rejet_annonce')->first();
    expect($action->notes)->toBe('Photos de mauvaise qualité.');
});

it('rejects validating a listing that is not pending', function () {
    $admin = User::factory()->admin()->create();
    $residence = Residence::factory()->rejetee()->create();

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/listings/residence/{$residence->id}/validate");

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'listing_not_pending');
});

it('returns 404 for an unknown listing type', function () {
    $admin = User::factory()->admin()->create();
    $residence = Residence::factory()->enValidation()->create();

    $this->actingAs($admin)
        ->postJson("/api/v1/admin/listings/unknown/{$residence->id}/validate")
        ->assertStatus(404);
});

it('lists pending residences and vehicles together', function () {
    $admin = User::factory()->admin()->create();
    $residence = Residence::factory()->enValidation()->create();
    $vehicle = Vehicle::factory()->enValidation()->create();
    $published = Residence::factory()->publiee()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/listings/pending');

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($residence->id)
        ->toContain($vehicle->id)
        ->not->toContain($published->id);
});
