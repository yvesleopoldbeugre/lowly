<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Catalogue — voir DATABASE.md §6.1.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residences', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('address');
            $table->string('city', 120);
            $table->smallInteger('capacity');
            $table->decimal('daily_rate', 10, 2);
            $table->jsonb('attributes')->nullable();
            $table->enum('status', ['brouillon', 'en_validation', 'publiee', 'rejetee', 'suspendue'])->default('brouillon');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city', 'status'], 'idx_residences_city_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residences');
    }
};
