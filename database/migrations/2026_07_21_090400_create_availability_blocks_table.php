<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Availability — voir DATABASE.md §7.
 *
 * Table polymorphique et générique, réutilisable pour toute nouvelle
 * catégorie d'offre future (voir ARCHITECTURE.md §13).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('blockable_type', 50);
            $table->uuid('blockable_id');
            $table->enum('origin', ['reservation', 'entretien', 'maintenance', 'usage_personnel', 'autre']);
            $table->foreignUuid('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['blockable_type', 'blockable_id'], 'idx_availability_blocks_blockable');
        });

        // Type `daterange` non supporté nativement par le Schema Builder Laravel.
        DB::statement('ALTER TABLE availability_blocks ADD COLUMN period daterange NOT NULL');

        // Contrainte d'intégrité critique n°1 (DATABASE.md §12.1) : empêche tout
        // chevauchement de période pour un même bien, garantie au niveau base de
        // données indépendamment de la couche applicative. Nécessite l'extension
        // `btree_gist` activée par la migration 2026_07_21_090000.
        DB::statement(<<<'SQL'
            ALTER TABLE availability_blocks
              ADD CONSTRAINT excl_availability_no_overlap
              EXCLUDE USING gist (
                blockable_type WITH =,
                blockable_id WITH =,
                period WITH &&
              )
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_blocks');
    }
};
