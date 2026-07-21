<?php

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Partners\Models\Partner;
use Illuminate\Support\Str;

it('deletes a manual block owned by the authenticated partner', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);
    $block = AvailabilityBlock::factory()->create([
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'origin' => 'entretien',
        'reservation_id' => null,
        'created_by' => $partner->user->id,
    ]);

    $this->actingAs($partner->user)
        ->deleteJson("/api/v1/partner/availability-blocks/{$block->id}")
        ->assertNoContent();

    expect(AvailabilityBlock::find($block->id))->toBeNull();
});

it('rejects deleting a block owned by another partner', function () {
    $partner = Partner::factory()->valide()->create();
    $other = AvailabilityBlock::factory()->manuel()->create();

    $this->actingAs($partner->user)
        ->deleteJson("/api/v1/partner/availability-blocks/{$other->id}")
        ->assertStatus(403);
});

it('rejects deleting a block created automatically by a reservation', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->create(['partner_id' => $partner->id]);
    $block = AvailabilityBlock::factory()->create([
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'origin' => 'reservation',
        'created_by' => $partner->user->id,
    ]);

    $this->actingAs($partner->user)
        ->deleteJson("/api/v1/partner/availability-blocks/{$block->id}")
        ->assertStatus(403);

    expect(AvailabilityBlock::find($block->id))->not->toBeNull();
});

it('returns 404 for an unknown block id', function () {
    $partner = Partner::factory()->valide()->create();

    $this->actingAs($partner->user)
        ->deleteJson('/api/v1/partner/availability-blocks/'.Str::uuid())
        ->assertStatus(404);
});
