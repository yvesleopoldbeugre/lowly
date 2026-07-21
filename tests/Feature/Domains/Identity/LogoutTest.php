<?php

use App\Domains\Identity\Models\User;

it('logs out and invalidates the session', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/v1/me')
        ->assertOk();

    $this->postJson('/api/v1/auth/logout')->assertNoContent();

    $this->assertGuest();
});

it('rejects logout for an unauthenticated request', function () {
    $this->postJson('/api/v1/auth/logout')->assertStatus(401);
});
