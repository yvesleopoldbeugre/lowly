<?php

use App\Domains\Administration\Models\PlatformSetting;
use App\Domains\Identity\Models\User;

it('lists platform settings', function () {
    $admin = User::factory()->admin()->create();
    PlatformSetting::factory()->create(['key' => 'reservation_response_delay_hours', 'value' => ['hours' => 48]]);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/settings');

    $response->assertOk();
    $response->assertJsonFragment(['key' => 'reservation_response_delay_hours']);
});

it('updates a platform setting by key', function () {
    $admin = User::factory()->admin()->create();
    PlatformSetting::factory()->create(['key' => 'counter_offer_response_delay_hours', 'value' => ['hours' => 72]]);

    $response = $this->actingAs($admin)->patchJson('/api/v1/admin/settings', [
        'key' => 'counter_offer_response_delay_hours',
        'value' => ['hours' => 96],
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.value.hours', 96);
    expect(PlatformSetting::where('key', 'counter_offer_response_delay_hours')->first()->value)->toBe(['hours' => 96]);
});

it('rejects updating an unknown setting key', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->patchJson('/api/v1/admin/settings', [
        'key' => 'unknown_setting',
        'value' => ['hours' => 1],
    ])->assertStatus(422);
});

it('rejects a non-admin user from the admin settings endpoint', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->getJson('/api/v1/admin/settings')->assertStatus(403);
});
