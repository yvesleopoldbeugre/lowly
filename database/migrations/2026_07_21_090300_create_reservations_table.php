<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Reservation — voir DATABASE.md §8.1.
 *
 * `reservable_type` / `reservable_id` forment une relation polymorphe vers
 * `residences` ou `vehicles` (voir ARCHITECTURE.md §13, extensibilité du
 * catalogue) : aucune contrainte de clé étrangère n'est donc posée sur ce
 * couple, conformément au principe d'abstraction "Offre réservable".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('reservable_type', 50);
            $table->uuid('reservable_id');
            $table->smallInteger('nights_count');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['en_attente', 'confirmee', 'refusee', 'contre_proposee', 'expiree'])->default('en_attente');
            $table->uuid('parent_reservation_id')->nullable();
            $table->timestamps();

            $table->index(['client_id'], 'idx_reservations_client_id');
            $table->index(['status'], 'idx_reservations_status');
            $table->index(['reservable_type', 'reservable_id'], 'idx_reservations_reservable');
        });

        // La clé étrangère auto-référencée est ajoutée dans un second temps :
        // dans un même Schema::create, Laravel exécute les commandes implicites
        // (index/primary fluides) après les commandes explicites (foreign()),
        // ce qui casse la FK sur `id` lorsqu'elle référence sa propre table.
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreign('parent_reservation_id')->references('id')->on('reservations')->nullOnDelete();
        });

        // Type `daterange` non supporté nativement par le Schema Builder Laravel.
        DB::statement('ALTER TABLE reservations ADD COLUMN period daterange NOT NULL');

        // Contraintes d'intégrité critiques — voir DATABASE.md §12.3 et §12.4.
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_amount_positive CHECK (total_amount > 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_period_valid CHECK (upper(period) > lower(period))');
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
