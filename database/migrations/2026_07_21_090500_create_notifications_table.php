<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Communication — voir DATABASE.md §9.1.
 *
 * Table métier propre à LOWLY (et non la table `notifications` polymorphe
 * générée par `php artisan notifications:table`) : un `user_id` direct
 * suffit pour le MVP, conformément au schéma documenté.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 80);
            $table->jsonb('payload');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['user_id', 'read_at'], 'idx_notifications_user_id_read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
