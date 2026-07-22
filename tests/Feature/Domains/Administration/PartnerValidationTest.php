<?php

use App\Domains\Administration\Models\AdminAction;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

it('validates a pending partner and notifies them', function () {
    $admin = User::factory()->admin()->create();
    $partner = Partner::factory()->create();

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/partners/{$partner->id}/validate");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'valide');

    expect($partner->fresh()->status)->toBe('valide')
        ->and($partner->fresh()->validated_by)->toBe($admin->id);

    expect(AdminAction::where('target_type', 'partner')->where('target_id', $partner->id)->where('action_type', 'validation_partenaire')->exists())->toBeTrue();
    expect(Notification::where('user_id', $partner->user_id)->where('type', 'partenaire_valide')->exists())->toBeTrue();
});

it('lets a validated partner publish a listing (validation partenaire — TESTING.md §7)', function () {
    $admin = User::factory()->admin()->create();
    $partner = Partner::factory()->create();
    $this->actingAs($admin)->postJson("/api/v1/admin/partners/{$partner->id}/validate")->assertOk();

    $residence = Residence::factory()->create(['partner_id' => $partner->id, 'status' => 'brouillon']);

    $response = $this->actingAs($partner->user)->patchJson("/api/v1/partner/residences/{$residence->id}", [
        'submit_for_validation' => true,
    ]);

    $response->assertOk();
    expect($residence->fresh()->status)->toBe('en_validation');
});

it('is idempotent when validating an already-validated partner', function () {
    $admin = User::factory()->admin()->create();
    $partner = Partner::factory()->valide()->create();

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/partners/{$partner->id}/validate");

    $response->assertOk();
    expect(AdminAction::where('target_id', $partner->id)->count())->toBe(0);
});

it('rejects validating a partner that was already rejected', function () {
    $admin = User::factory()->admin()->create();
    $partner = Partner::factory()->rejete()->create();

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/partners/{$partner->id}/validate");

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'partner_not_pending');
});

it('rejects a pending partner with optional notes', function () {
    $admin = User::factory()->admin()->create();
    $partner = Partner::factory()->create();

    $response = $this->actingAs($admin)->postJson("/api/v1/admin/partners/{$partner->id}/reject", [
        'notes' => 'Document illisible.',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'rejete');

    $action = AdminAction::where('target_id', $partner->id)->where('action_type', 'rejet_partenaire')->first();
    expect($action->notes)->toBe('Document illisible.');
});

it('lists only pending partners', function () {
    $admin = User::factory()->admin()->create();
    $pending = Partner::factory()->create();
    $validated = Partner::factory()->valide()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/partners/pending');

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($pending->id)->not->toContain($validated->id);
});

it('exposes whether a legal document was provided', function () {
    $admin = User::factory()->admin()->create();
    $withDoc = Partner::factory()->create(['legal_document_path' => 'documents/foo.pdf']);
    $withoutDoc = Partner::factory()->create(['legal_document_path' => null]);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/partners/pending');

    $response->assertOk();
    $byId = collect($response->json('data'))->keyBy('id');
    expect($byId[$withDoc->id]['attributes']['has_legal_document'])->toBeTrue()
        ->and($byId[$withoutDoc->id]['attributes']['has_legal_document'])->toBeFalse();
});

it('rejects a non-admin user from the admin partners endpoint', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->getJson('/api/v1/admin/partners/pending')->assertStatus(403);
});
