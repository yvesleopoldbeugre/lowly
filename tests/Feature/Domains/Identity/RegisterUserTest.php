<?php

use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;

it('registers a new client and opens the session immediately', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'full_name' => 'Amara Koné',
        'email' => 'amara@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '+2250700000000',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.type', 'user');
    $response->assertJsonPath('data.attributes.email', 'amara@example.com');
    $response->assertJsonPath('data.attributes.role', 'client');

    $this->assertAuthenticated();

    $user = User::where('email', 'amara@example.com')->firstOrFail();
    expect($user->role)->toBe('client')
        ->and($user->full_name)->toBe('Amara Koné');
});

it('rejects registration with an already used email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/v1/auth/register', [
        'full_name' => 'Karim Traoré',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('error.code', 'validation_failed');
    $response->assertJsonPath('error.details.email.0', fn ($message) => is_string($message));

    $this->assertGuest();
});

it('rejects registration when the password confirmation does not match', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'full_name' => 'Fatou Diarra',
        'email' => 'fatou@example.com',
        'password' => 'password123',
        'password_confirmation' => 'mismatch',
    ]);

    $response->assertStatus(422);
    $this->assertGuest();
    expect(User::where('email', 'fatou@example.com')->exists())->toBeFalse();
});

it('creates a pending partner profile when wants_partner is true', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'full_name' => 'Karim Partenaire',
        'email' => 'karim@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'wants_partner' => true,
    ]);

    $response->assertCreated();

    $user = User::where('email', 'karim@example.com')->firstOrFail();
    $partner = Partner::where('user_id', $user->id)->first();

    expect($partner)->not->toBeNull()
        ->and($partner->status)->toBe('en_attente');
});

it('does not create a partner profile when wants_partner is omitted', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'full_name' => 'Simple Client',
        'email' => 'client@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated();

    $user = User::where('email', 'client@example.com')->firstOrFail();
    expect(Partner::where('user_id', $user->id)->exists())->toBeFalse();
});
