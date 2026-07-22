<?php

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Models\ReservationStatusHistory;

it('confirms a reservation and blocks exactly the requested period', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'period' => ['start' => '2026-08-10', 'end' => '2026-08-13'],
    ]);

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/accept");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'confirmee');

    $block = AvailabilityBlock::where('reservation_id', $reservation->id)->firstOrFail();
    expect($block->blockable_id)->toBe($residence->id)
        ->and($block->origin)->toBe('reservation')
        ->and($block->period['start']->toDateString())->toBe('2026-08-10')
        ->and($block->period['end']->toDateString())->toBe('2026-08-13');

    expect(ReservationStatusHistory::where('reservation_id', $reservation->id)->where('new_status', 'confirmee')->exists())->toBeTrue();
    expect(Notification::where('user_id', $reservation->client_id)->where('type', 'reservation_confirmee')->exists())->toBeTrue();
});

it('rejects confirming a reservation that overlaps an existing block (race condition)', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);

    AvailabilityBlock::factory()->create([
        'blockable_type' => 'residence',
        'blockable_id' => $residence->id,
        'origin' => 'entretien',
        'reservation_id' => null,
        'created_by' => $partner->user->id,
        'period' => ['start' => '2026-08-10', 'end' => '2026-08-13'],
    ]);

    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'period' => ['start' => '2026-08-11', 'end' => '2026-08-12'],
    ]);

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/accept");

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'availability_block_overlap');
});

it('rejects a reservation without an alternative and closes the cycle', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
    ]);

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/reject");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.status', 'refusee');
    expect(Notification::where('user_id', $reservation->client_id)->where('type', 'reservation_refusee')->exists())->toBeTrue();
});

it('submits a counter-offer on an alternative listing of the same partner', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $alternative = Vehicle::factory()->publie()->create(['partner_id' => $partner->id]);
    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
    ]);

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/counter-offer", [
        'proposed_reservable_type' => 'vehicle',
        'proposed_reservable_id' => $alternative->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-04',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.status', 'en_attente');

    expect($reservation->fresh()->status)->toBe('contre_proposee');
    expect(CounterOffer::where('original_reservation_id', $reservation->id)->exists())->toBeTrue();
    expect(Notification::where('user_id', $reservation->client_id)->where('type', 'contre_proposition_recue')->exists())->toBeTrue();
});

it('returns 404 when the alternative listing does not belong to the responding partner', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $someoneElsesListing = Residence::factory()->publiee()->create();
    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
    ]);

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/counter-offer", [
        'proposed_reservable_type' => 'residence',
        'proposed_reservable_id' => $someoneElsesListing->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-04',
    ]);

    $response->assertStatus(404);
});

it('rejects a counter-offer on a listing overlapping an existing block', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $alternative = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
    ]);

    AvailabilityBlock::factory()->create([
        'blockable_type' => 'residence',
        'blockable_id' => $alternative->id,
        'origin' => 'entretien',
        'reservation_id' => null,
        'created_by' => $partner->user->id,
        'period' => ['start' => '2026-09-01', 'end' => '2026-09-05'],
    ]);

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/counter-offer", [
        'proposed_reservable_type' => 'residence',
        'proposed_reservable_id' => $alternative->id,
        'start_date' => '2026-09-02',
        'end_date' => '2026-09-03',
    ]);

    $response->assertStatus(409);
    $response->assertJsonPath('error.code', 'reservation_period_unavailable');
});

it('rejects a counter-offer whose start date falls on or after the original reservation end date without an explicit end date', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $alternative = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'period' => ['start' => '2026-08-10', 'end' => '2026-08-13'],
    ]);

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/counter-offer", [
        'proposed_reservable_type' => 'residence',
        'proposed_reservable_id' => $alternative->id,
        'start_date' => '2026-08-14',
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('error.code', 'reservation_invalid_period');
});

it('rejects a partner acting on a reservation for a listing they do not own', function () {
    $partner = Partner::factory()->valide()->create();
    $reservation = Reservation::factory()->create();

    $response = $this->actingAs($partner->user)->postJson("/api/v1/partner/reservations/{$reservation->id}/accept");

    $response->assertStatus(403);
    $response->assertJsonPath('error.code', 'reservation_not_owned');
});

it('scopes the partner reservations index to their own listings only', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $mine = Reservation::factory()->create(['reservable_type' => 'residence', 'reservable_id' => $residence->id]);
    $notMine = Reservation::factory()->create();

    $response = $this->actingAs($partner->user)->getJson('/api/v1/partner/reservations');

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($mine->id)->not->toContain($notMine->id);
});

it('rejects a non-partner user from accepting a reservation', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->create();

    $this->actingAs($client)->postJson("/api/v1/partner/reservations/{$reservation->id}/accept")->assertStatus(403);
});
