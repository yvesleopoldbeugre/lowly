<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Reservation — voir DATABASE.md §8.2.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('previous_status', 30)->nullable();
            $table->string('new_status', 30);
            // Nullable : une transition déclenchée par le job planifié
            // d'expiration (voir BUSINESS_RULES.md §6.2) n'a pas d'auteur
            // humain — voir ExpirerContrePropositionsAction.
            $table->foreignUuid('changed_by')->nullable()->constrained('users');
            $table->timestamp('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_status_history');
    }
};
