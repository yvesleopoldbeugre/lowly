<?php

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Partners\Models\Partner;
use Illuminate\Support\Str;

it('creates a manual block on the partner\'s own residence', function (string $origin) {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)->postJson('/api/v1/partner/availability-blocks', [
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-05',
        'origin' => $origin,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.origin', $origin);
    $response->assertJsonPath('data.attributes.period.start', '2026-08-01');
    $response->assertJsonPath('data.attributes.period.end', '2026-08-05');

    $block = AvailabilityBlock::firstOrFail();
    expect($block->blockable_id)->toBe($residence->id)
        ->and($block->created_by)->toBe($partner->user->id);
})->with(['entretien', 'maintenance', 'usage_personnel', 'autre']);

it('creates a manual block on the partner\'s own vehicle', function () {
    $partner = Partner::factory()->valide()->create();
    $vehicle = Vehicle::factory()->create(['partner_id' => $partner->id]);

    $response = $this->actingAs($partner->user)->postJson('/api/v1/partner/availability-blocks', [
        'blockable_type' => 'vehicle',
        'blockable_id' => $vehicle->id,
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-03',
        'origin' => 'entretien',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.relationships.blockable.type', 'vehicle');
});

it('rejects blocking a residence owned by another partner', function () {
    $partner = Partner::factory()->valide()->create();
    $other = Residence::factory()->create();

    $this->actingAs($partner->user)->postJson('/api/v1/partner/availability-blocks', [
        'blockable_type' => 'residence',
        'blockable_id' => $other->id,
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-05',
        'origin' => 'entretien',
    ])->assertStatus(403);
});

it('returns 404 for an unknown blockable id', function () {
    $partner = Partner::factory()->valide()->create();

    $this->actingAs($partner->user)->postJson('/api/v1/partner/availability-blocks', [
        'blockable_type' => 'residence',
        'blockable_id' => (string) Str::uuid(),
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-05',
        'origin' => 'entretien',
    ])->assertStatus(404);
});

it('rejects an end date that is not after the start date', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)->postJson('/api/v1/partner/availability-blocks', [
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'start_date' => '2026-08-05',
        'end_date' => '2026-08-05',
        'origin' => 'entretien',
    ])->assertStatus(422);
});

it('rejects origin=reservation on the manual endpoint', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);

    $this->actingAs($partner->user)->postJson('/api/v1/partner/availability-blocks', [
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-05',
        'origin' => 'reservation',
    ])->assertStatus(422);
});

it('rejects a period overlapping an existing block on the same property', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);

    AvailabilityBlock::factory()->create([
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'origin' => 'entretien',
        'reservation_id' => null,
        'created_by' => $partner->user->id,
        'period' => ['start' => '2026-08-01', 'end' => '2026-08-10'],
    ]);

    $response = $this->actingAs($partner->user)->postJson('/api/v1/partner/availability-blocks', [
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'start_date' => '2026-08-05',
        'end_date' => '2026-08-15',
        'origin' => 'maintenance',
    ]);

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'availability_block_overlap');
});
