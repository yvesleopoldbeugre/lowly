<?php

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\Reservation;
use Illuminate\Support\Str;

it('computes nights and total amount using the 12h-12h reference example (10 to 13 january = 3 nights)', function () {
    $client = User::factory()->client()->create();
    $residence = Residence::factory()->publiee()->create(['daily_rate' => 45]);

    $response = $this->actingAs($client)->postJson('/api/v1/reservations', [
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'start_date' => '2026-08-10',
        'end_date' => '2026-08-13',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.nights_count', 3);
    $response->assertJsonPath('data.attributes.total_amount', '135.00');
    $response->assertJsonPath('data.attributes.status', 'en_attente');
    $response->assertJsonPath('data.attributes.period.start', '2026-08-10');
    $response->assertJsonPath('data.attributes.period.end', '2026-08-13');

    expect(Reservation::firstOrFail()->client_id)->toBe($client->id);
});

it('computes a single night correctly', function () {
    $client = User::factory()->client()->create();
    $vehicle = Vehicle::factory()->publie()->create(['daily_rate' => 30]);

    $response = $this->actingAs($client)->postJson('/api/v1/reservations', [
        'reservable_type' => 'vehicle',
        'reservable_id' => $vehicle->id,
        'start_date' => '2026-08-10',
        'end_date' => '2026-08-11',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.nights_count', 1);
    $response->assertJsonPath('data.attributes.total_amount', '30.00');
});

it('computes a long stay correctly', function () {
    $client = User::factory()->client()->create();
    $residence = Residence::factory()->publiee()->create(['daily_rate' => 20]);

    $response = $this->actingAs($client)->postJson('/api/v1/reservations', [
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-31',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.nights_count', 30);
    $response->assertJsonPath('data.attributes.total_amount', '600.00');
});

it('does not create any availability block when a request is submitted', function () {
    $client = User::factory()->client()->create();
    $residence = Residence::factory()->publiee()->create();

    $this->actingAs($client)->postJson('/api/v1/reservations', [
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'start_date' => '2026-08-10',
        'end_date' => '2026-08-13',
    ])->assertCreated();

    expect(AvailabilityBlock::count())->toBe(0);
});

it('returns 404 for an unpublished residence', function () {
    $client = User::factory()->client()->create();
    $residence = Residence::factory()->create();

    $this->actingAs($client)->postJson('/api/v1/reservations', [
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'start_date' => '2026-08-10',
        'end_date' => '2026-08-13',
    ])->assertStatus(404);
});

it('returns 404 for an unknown reservable id', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->postJson('/api/v1/reservations', [
        'reservable_type' => 'residence',
        'reservable_id' => (string) Str::uuid(),
        'start_date' => '2026-08-10',
        'end_date' => '2026-08-13',
    ])->assertStatus(404);
});

it('rejects a second request overlapping a period already blocked', function () {
    $client = User::factory()->client()->create();
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);

    AvailabilityBlock::factory()->create([
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'origin' => 'reservation',
        'created_by' => $client->id,
        'period' => ['start' => '2026-08-10', 'end' => '2026-08-13'],
    ]);

    $response = $this->actingAs($client)->postJson('/api/v1/reservations', [
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'start_date' => '2026-08-12',
        'end_date' => '2026-08-15',
    ]);

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'reservation_period_unavailable');
});

it('rejects a client role from partner-only endpoints', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->getJson('/api/v1/partner/reservations')->assertStatus(403);
});
