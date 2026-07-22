<?php

use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Models\ReservationStatusHistory;

it('computes platform statistics from existing data', function () {
    $admin = User::factory()->admin()->create();

    $confirmed = Reservation::factory()->confirmee()->create();
    AvailabilityBlock::factory()->create([
        'blockable_type' => $confirmed->reservable_type,
        'blockable_id' => $confirmed->reservable_id,
        'origin' => 'reservation',
        'reservation_id' => $confirmed->id,
        'period' => $confirmed->period,
    ]);
    $refused = Reservation::factory()->refusee()->create();

    ReservationStatusHistory::factory()->create([
        'reservation_id' => $confirmed->id,
        'previous_status' => 'en_attente',
        'new_status' => 'confirmee',
        'changed_at' => $confirmed->created_at->addHours(2),
    ]);
    ReservationStatusHistory::factory()->create([
        'reservation_id' => $refused->id,
        'previous_status' => 'en_attente',
        'new_status' => 'refusee',
        'changed_at' => $refused->created_at->addHours(4),
    ]);

    // original_reservation_id/proposed_reservable_* épinglés sur les
    // réservations/biens déjà créés ci-dessus : les valeurs par défaut de
    // CounterOfferFactory créeraient sinon de nouvelles Reservation/Residence
    // (et donc de nouveaux partenaires validés), qui fausseraient les taux.
    CounterOffer::factory()->acceptee()->create([
        'original_reservation_id' => $confirmed->id,
        'proposed_reservable_type' => $confirmed->reservable_type,
        'proposed_reservable_id' => $confirmed->reservable_id,
    ]);
    CounterOffer::factory()->refusee()->create([
        'original_reservation_id' => $refused->id,
        'proposed_reservable_type' => $refused->reservable_type,
        'proposed_reservable_id' => $refused->reservable_id,
    ]);

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/statistics');

    $response->assertOk();
    $attributes = $response->json('data.attributes');

    expect($attributes['reservation_acceptance_rate'])->toBe(0.5)
        ->and((float) $attributes['average_partner_response_delay_hours'])->toBe(3.0)
        ->and($attributes['counter_offer_acceptance_rate'])->toBe(0.5)
        ->and((float) $attributes['confirmed_reservations_calendar_accuracy_rate'])->toBe(1.0)
        ->and($attributes)->not->toHaveKey('search_to_request_conversion_rate');
});

it('counts only validated partners as active', function () {
    $admin = User::factory()->admin()->create();

    Partner::factory()->valide()->count(2)->create();
    Partner::factory()->create();
    Partner::factory()->rejete()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/statistics');

    $response->assertOk();
    expect($response->json('data.attributes.active_validated_partners'))->toBe(2);
});

it('returns null rates when there is no data yet', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->getJson('/api/v1/admin/statistics');

    $response->assertOk();
    $attributes = $response->json('data.attributes');

    expect($attributes['reservation_acceptance_rate'])->toBeNull()
        ->and($attributes['average_partner_response_delay_hours'])->toBeNull()
        ->and($attributes['active_validated_partners'])->toBe(0);
});

it('rejects a non-admin user from the statistics endpoint', function () {
    $client = User::factory()->client()->create();

    $this->actingAs($client)->getJson('/api/v1/admin/statistics')->assertStatus(403);
});
