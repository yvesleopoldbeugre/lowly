<?php

use App\Domains\Administration\Models\AdminAction;
use App\Domains\Identity\Models\User;

it('lists users filtered by role and status', function () {
    $admin = User::factory()->admin()->create();
    $client = User::factory()->client()->create();
    $suspendedPartner = User::factory()->partner()->create(['suspended_at' => now()]);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/users?role=client');
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($client->id)->not->toContain($suspendedPartner->id);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/users?status=suspended');
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($suspendedPartner->id)->not->toContain($client->id);
});

it('suspends a user and logs the admin action', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->client()->create();

    $response = $this->actingAs($admin)->patchJson("/api/v1/admin/users/{$user->id}/suspend");

    $response->assertOk();
    expect($user->fresh()->suspended_at)->not->toBeNull();
    expect(AdminAction::where('target_id', $user->id)->where('action_type', 'suspension_utilisateur')->exists())->toBeTrue();
});

it('is idempotent when suspending an already-suspended user', function () {
    $admin = User::factory()->admin()->create();
    $suspendedAt = now()->subDay();
    $user = User::factory()->client()->create(['suspended_at' => $suspendedAt]);

    $this->actingAs($admin)->patchJson("/api/v1/admin/users/{$user->id}/suspend")->assertOk();

    expect($user->fresh()->suspended_at->timestamp)->toBe($suspendedAt->timestamp);
    expect(AdminAction::where('target_id', $user->id)->count())->toBe(0);
});

it('blocks a suspended user from logging in', function () {
    $user = User::factory()->create(['suspended_at' => now()]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(403);
    $response->assertJsonPath('error.code', 'account_suspended');
    $this->assertGuest();
});

it('rejects a non-admin user from the admin users endpoint', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->getJson('/api/v1/admin/users')->assertStatus(403);
});
