<?php

use App\Domains\Identity\Models\User;

it('rejects unauthenticated access to the profile', function () {
    $this->getJson('/api/v1/me')->assertStatus(401);
});

it('returns the profile of the authenticated user', function () {
    $user = User::factory()->create(['full_name' => 'Amara Koné']);

    $response = $this->actingAs($user)->getJson('/api/v1/me');

    $response->assertOk();
    $response->assertJsonPath('data.id', $user->id);
    $response->assertJsonPath('data.attributes.full_name', 'Amara Koné');
});

it('lets any authenticated role access its own profile', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->getJson('/api/v1/me')->assertOk();
})->with(['client', 'partner', 'admin']);

it('updates the profile of the authenticated user', function () {
    $user = User::factory()->create(['full_name' => 'Amara Koné', 'phone' => null]);

    $response = $this->actingAs($user)->patchJson('/api/v1/me', [
        'full_name' => 'Amara Koné-Diallo',
        'phone' => '+2250700000000',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.full_name', 'Amara Koné-Diallo');

    expect($user->refresh()->full_name)->toBe('Amara Koné-Diallo')
        ->and($user->phone)->toBe('+2250700000000');
});

it('rejects updating the profile with an email already used by another user', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create(['email' => 'me@example.com']);

    $response = $this->actingAs($user)->patchJson('/api/v1/me', [
        'email' => 'taken@example.com',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('error.code', 'validation_failed');
    expect($user->refresh()->email)->toBe('me@example.com');
});
