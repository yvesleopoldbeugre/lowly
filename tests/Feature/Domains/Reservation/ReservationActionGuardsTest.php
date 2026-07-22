<?php

use App\Domains\Catalogue\Models\Residence;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Actions\CreerDemandeReservationAction;
use App\Domains\Reservation\Actions\RefuserAvecContrePropositionAction;
use App\Domains\Reservation\Exceptions\ReservationInvalidPeriodException;
use App\Domains\Reservation\Models\Reservation;

/**
 * Couvre les gardes défensives au niveau Action pour la même journée
 * calendaire (BUSINESS_RULES.md §10) — non atteignables via l'API HTTP
 * grâce à la validation de FormRequest, mais garanties ici indépendamment.
 */
it('rejects a same-day period at the action level', function () {
    $client = User::factory()->client()->create();
    $residence = Residence::factory()->publiee()->create();

    app(CreerDemandeReservationAction::class)->executer($client, [
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
        'start_date' => '2026-08-10',
        'end_date' => '2026-08-10',
    ]);
})->throws(ReservationInvalidPeriodException::class);

it('rejects a counter-offer resolving to a same-day period at the action level', function () {
    $partner = Partner::factory()->valide()->create();
    $residence = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $alternative = Residence::factory()->publiee()->create(['partner_id' => $partner->id]);
    $reservation = Reservation::factory()->create([
        'reservable_type' => 'residence',
        'reservable_id' => $residence->id,
    ]);

    app(RefuserAvecContrePropositionAction::class)->executer($reservation, $partner->user, [
        'proposed_reservable_type' => 'residence',
        'proposed_reservable_id' => $alternative->id,
        'start_date' => '2026-09-01',
        'end_date' => '2026-09-01',
    ]);
})->throws(ReservationInvalidPeriodException::class);
