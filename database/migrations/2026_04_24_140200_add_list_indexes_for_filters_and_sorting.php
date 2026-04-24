<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->index(['is_public', 'start_at'], 'hackatons_public_start_idx');
            $table->index(['start_at', 'end_at'], 'hackatons_schedule_idx');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->index(['hackaton_id', 'is_public'], 'teams_hackaton_public_idx');
        });

        Schema::table('team_roles', function (Blueprint $table) {
            $table->index(['team_id', 'user_id'], 'team_roles_team_user_idx');
            $table->index(['team_id', 'role_id'], 'team_roles_team_role_idx');
        });
    }

    public function down(): void
    {
        Schema::table('team_roles', function (Blueprint $table) {
            $table->dropIndex('team_roles_team_user_idx');
            $table->dropIndex('team_roles_team_role_idx');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex('teams_hackaton_public_idx');
        });

        Schema::table('hackatons', function (Blueprint $table) {
            $table->dropIndex('hackatons_public_start_idx');
            $table->dropIndex('hackatons_schedule_idx');
        });
    }
};
