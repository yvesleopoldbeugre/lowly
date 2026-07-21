<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Catalogue — voir DATABASE.md §6.3.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('brand', 120);
            $table->string('model', 120);
            $table->smallInteger('year')->nullable();
            $table->string('plate_number', 30)->nullable();
            $table->decimal('daily_rate', 10, 2);
            $table->jsonb('attributes')->nullable();
            $table->enum('status', ['brouillon', 'en_validation', 'publie', 'rejete', 'suspendu'])->default('brouillon');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status'], 'idx_vehicles_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
