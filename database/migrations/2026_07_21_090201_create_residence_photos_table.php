<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Catalogue — voir DATABASE.md §6.2.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residence_photos', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('residence_id')->constrained('residences')->cascadeOnDelete();
            $table->string('path');
            $table->smallInteger('position')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residence_photos');
    }
};
