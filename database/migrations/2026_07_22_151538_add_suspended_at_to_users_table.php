<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Domaine Administration — voir API_GUIDE.md §12
 * (`PATCH /api/v1/admin/users/{id}/suspend`). Distinct de `deleted_at`
 * (SoftDeletes) : une suspension est une restriction réversible décidée
 * par un administrateur, pas une suppression.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('suspended_at');
        });
    }
};
