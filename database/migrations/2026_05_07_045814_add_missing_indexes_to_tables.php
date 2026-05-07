<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('team_social_links', function (Blueprint $table) {
            $table->index('team_id');
        });

        Schema::table('avatar_presets', function (Blueprint $table) {
            $table->index(['avatar_preset_pack_id', 'sort_order']);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('avatar_presets', function (Blueprint $table) {
            $table->dropIndex(['avatar_preset_pack_id', 'sort_order']);
        });

        Schema::table('team_social_links', function (Blueprint $table) {
            $table->dropIndex(['team_id']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
