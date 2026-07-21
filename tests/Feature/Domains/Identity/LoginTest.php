<?php

use App\Domains\Identity\Models\User;

it('logs in with valid credentials and opens the session', function () {
    User::factory()->create([
        'email' => 'amara@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'amara@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.email', 'amara@example.com');
    $this->assertAuthenticated();
});

it('rejects login with a wrong password without revealing which field is wrong', function () {
    User::factory()->create([
        'email' => 'amara@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'amara@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
    $response->assertJsonPath('error.code', 'invalid_credentials');
    $this->assertGuest();
});

it('rejects login with an unknown email using the same error code', function () {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'unknown@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(401);
    $response->assertJsonPath('error.code', 'invalid_credentials');
    $this->assertGuest();
});

it('rate limits login after 5 attempts per minute per IP', function () {
    User::factory()->create([
        'email' => 'amara@example.com',
        'password' => 'password123',
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'amara@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(401);
    }

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'amara@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
    $response->assertJsonPath('error.code', 'too_many_requests');
    $response->assertHeader('Retry-After');
});
