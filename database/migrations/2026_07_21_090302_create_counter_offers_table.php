<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Reservation — voir DATABASE.md §8.3.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counter_offers', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('original_reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('proposed_reservable_type', 50);
            $table->uuid('proposed_reservable_id');
            $table->enum('status', ['en_attente', 'acceptee', 'refusee', 'expiree'])->default('en_attente');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        // Type `daterange` non supporté nativement par le Schema Builder Laravel.
        DB::statement('ALTER TABLE counter_offers ADD COLUMN proposed_period daterange NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('counter_offers');
    }
};
