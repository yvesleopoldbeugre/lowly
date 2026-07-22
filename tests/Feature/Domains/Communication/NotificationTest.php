<?php

use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;

it('lists only the notifications of the authenticated user', function () {
    $user = User::factory()->create();
    $mine = Notification::factory()->create(['user_id' => $user->id]);
    $notMine = Notification::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/notifications');

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($mine->id)->not->toContain($notMine->id);
});

it('marks an unread notification as read', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->patchJson("/api/v1/notifications/{$notification->id}/read");

    $response->assertOk();
    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('is idempotent when marking an already-read notification as read', function () {
    $user = User::factory()->create();
    $readAt = now()->subHour();
    $notification = Notification::factory()->lue()->create(['user_id' => $user->id, 'read_at' => $readAt]);

    $response = $this->actingAs($user)->patchJson("/api/v1/notifications/{$notification->id}/read");

    $response->assertOk();
    expect($notification->fresh()->read_at->timestamp)->toBe($readAt->timestamp);
});

it('rejects marking another user\'s notification as read', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create();

    $this->actingAs($user)
        ->patchJson("/api/v1/notifications/{$notification->id}/read")
        ->assertStatus(403);

    expect($notification->fresh()->read_at)->toBeNull();
});
