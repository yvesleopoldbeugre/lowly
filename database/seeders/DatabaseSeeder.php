<?php

namespace Database\Seeders;

use App\Domains\Administration\Models\AdminAction;
use App\Domains\Administration\Models\PlatformSetting;
use App\Domains\Availability\Models\AvailabilityBlock;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Domains\Communication\Models\Notification;
use App\Domains\Identity\Models\User;
use App\Domains\Partners\Models\Partner;
use App\Domains\Reservation\Models\CounterOffer;
use App\Domains\Reservation\Models\Reservation;
use App\Domains\Reservation\Models\ReservationStatusHistory;
use Database\Factories\ResidencePhotoFactory;
use Database\Factories\VehiclePhotoFactory;
use Illuminate\Database\Seeder;

/**
 * Jeu de données de démonstration pour l'environnement `local` — voir
 * DATABASE.md §14 et DEPLOYMENT.md §5. N'est jamais exécuté en `production`.
 *
 * N'utilise volontairement PAS le trait `WithoutModelEvents` du squelette
 * Laravel par défaut : les clés primaires UUID de ce projet sont générées
 * à la création via le trait `HasUuids` (événement Eloquent `creating`,
 * voir DATABASE.md §2). Suspendre les événements empêcherait ces UUID
 * d'être renseignés côté PHP juste après `create()`, cassant toutes les
 * associations en cascade de ce seeder (une simple valeur par défaut
 * `gen_random_uuid()` en base ne suffit pas : Eloquent ne réinterroge pas
 * la ligne insérée pour une clé non auto-incrémentée).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('Les seeders de démonstration ne doivent jamais être exécutés en production (voir DEPLOYMENT.md §5).');

            return;
        }

        // --- Administrateurs -------------------------------------------------
        $admins = User::factory()->admin()->count(2)->create();
        $mainAdmin = $admins->first();

        User::factory()->admin()->create([
            'full_name' => 'Yves Administrateur',
            'email' => 'admin@lowly.test',
        ]);

        // --- Clients -----------------------------------------------------------
        $clients = User::factory()->client()->count(15)->create();

        User::factory()->client()->create([
            'full_name' => 'Amara Cliente',
            'email' => 'amara@lowly.test',
        ]);

        // --- Partenaires validés, avec résidences et véhicules publiés --------
        $validatedPartners = Partner::factory()
            ->valide()
            ->count(5)
            ->create();

        foreach ($validatedPartners as $partner) {
            Residence::factory()->publiee()->count(2)->create(['partner_id' => $partner->id])
                ->each(fn (Residence $residence) => ResidencePhotoFactory::new()->count(3)->create(['residence_id' => $residence->id]));

            Residence::factory()->enValidation()->create(['partner_id' => $partner->id]);

            Vehicle::factory()->publie()->count(2)->create(['partner_id' => $partner->id])
                ->each(fn (Vehicle $vehicle) => VehiclePhotoFactory::new()->count(2)->create(['vehicle_id' => $vehicle->id]));

            AdminAction::factory()->create([
                'admin_id' => $mainAdmin->id,
                'action_type' => 'validation_partenaire',
                'target_type' => 'partner',
                'target_id' => $partner->id,
                'notes' => 'Dossier complet, validation accordée.',
            ]);
        }

        // --- Un partenaire en attente et un partenaire rejeté, pour la file de
        //     validation de l'administrateur -----------------------------------
        $pendingPartner = Partner::factory()->create();
        Residence::factory()->enValidation()->create(['partner_id' => $pendingPartner->id]);

        Partner::factory()->rejete()->create();

        $publishedResidences = Residence::where('status', 'publiee')->get();
        $publishedVehicles = Vehicle::where('status', 'publie')->get();

        // --- Réservations à l'état EN_ATTENTE -----------------------------------
        foreach ($clients->take(4) as $client) {
            Reservation::factory()->create([
                'client_id' => $client->id,
                'reservable_type' => 'residence',
                'reservable_id' => $publishedResidences->random()->id,
                'status' => 'en_attente',
            ]);
        }

        // --- Réservations CONFIRMÉES, avec blocage calendrier et historique ----
        //     Chaque réservation confirmée porte sur un bien dédié afin d'éviter
        //     tout chevauchement avec la contrainte d'exclusion GiST de
        //     `availability_blocks` (voir DATABASE.md §7.2).
        foreach ($clients->skip(4)->take(4) as $client) {
            $residence = Residence::factory()->publiee()->create([
                'partner_id' => $validatedPartners->random()->id,
            ]);

            $reservation = Reservation::factory()->confirmee()->create([
                'client_id' => $client->id,
                'reservable_type' => 'residence',
                'reservable_id' => $residence->id,
            ]);

            AvailabilityBlock::factory()->create([
                'blockable_type' => 'residence',
                'blockable_id' => $residence->id,
                'period' => $reservation->period,
                'origin' => 'reservation',
                'reservation_id' => $reservation->id,
                'created_by' => $residence->partner->user_id,
            ]);

            ReservationStatusHistory::factory()->create([
                'reservation_id' => $reservation->id,
                'previous_status' => 'en_attente',
                'new_status' => 'confirmee',
                'changed_by' => $residence->partner->user_id,
                'changed_at' => $reservation->created_at,
            ]);

            Notification::factory()->create([
                'user_id' => $client->id,
                'type' => 'reservation_confirmee',
                'payload' => ['reservation_id' => $reservation->id],
            ]);
        }

        // --- Réservations REFUSÉES ------------------------------------------------
        foreach ($clients->skip(8)->take(2) as $client) {
            $reservation = Reservation::factory()->refusee()->create([
                'client_id' => $client->id,
                'reservable_type' => 'residence',
                'reservable_id' => $publishedResidences->random()->id,
            ]);

            ReservationStatusHistory::factory()->create([
                'reservation_id' => $reservation->id,
                'previous_status' => 'en_attente',
                'new_status' => 'refusee',
                'changed_by' => $mainAdmin->id,
                'changed_at' => $reservation->created_at,
            ]);
        }

        // --- Réservations CONTRE_PROPOSÉES, avec contre-proposition en attente -
        foreach ($clients->skip(10)->take(2) as $client) {
            $reservation = Reservation::factory()->contreProposee()->create([
                'client_id' => $client->id,
                'reservable_type' => 'residence',
                'reservable_id' => $publishedResidences->random()->id,
            ]);

            CounterOffer::factory()->create([
                'original_reservation_id' => $reservation->id,
                'proposed_reservable_type' => 'residence',
                'proposed_reservable_id' => $publishedResidences->random()->id,
            ]);

            Notification::factory()->create([
                'user_id' => $client->id,
                'type' => 'contre_proposition_recue',
                'payload' => ['reservation_id' => $reservation->id],
            ]);
        }

        // --- Une contre-proposition expirée, pour couvrir cet état ---------------
        $expiredReservation = Reservation::factory()->expiree()->create([
            'client_id' => $clients->last()->id,
            'reservable_type' => 'vehicle',
            'reservable_id' => $publishedVehicles->random()->id,
        ]);

        CounterOffer::factory()->expiree()->create([
            'original_reservation_id' => $expiredReservation->id,
            'proposed_reservable_type' => 'vehicle',
            'proposed_reservable_id' => $publishedVehicles->random()->id,
        ]);

        // --- Réservation sur véhicule, à l'état EN_ATTENTE ------------------------
        Reservation::factory()->forVehicle()->create([
            'client_id' => $clients->random()->id,
        ]);

        // --- Blocages manuels de calendrier véhicule (entretien / maintenance /
        //     usage personnel) — voir BUSINESS_RULES.md §4.2 ------------------------
        foreach (['entretien', 'maintenance', 'usage_personnel'] as $motif) {
            $vehicle = Vehicle::factory()->publie()->create([
                'partner_id' => $validatedPartners->random()->id,
            ]);

            AvailabilityBlock::factory()->create([
                'blockable_type' => 'vehicle',
                'blockable_id' => $vehicle->id,
                'origin' => $motif,
                'reservation_id' => null,
                'created_by' => $vehicle->partner->user_id,
            ]);
        }

        // --- Paramètres plateforme --------------------------------------------
        PlatformSetting::factory()->create([
            'key' => 'reservation_response_delay_hours',
            'value' => ['hours' => 48],
        ]);

        PlatformSetting::factory()->create([
            'key' => 'counter_offer_response_delay_hours',
            'value' => ['hours' => 72],
        ]);
    }
}
