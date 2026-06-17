<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->boolean('is_results_public')->default(false)->after('auto_publish_results_announcement');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('open_to_teams')->default(false)->after('show_phone_on_profile');
            $table->boolean('show_skills_on_profile')->default(false)->after('open_to_teams');
        });

        Schema::create('hackaton_watches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hackaton_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'hackaton_id']);
        });

        Schema::create('skill_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();

            $table->primary(['user_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_user');
        Schema::dropIfExists('hackaton_watches');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['open_to_teams', 'show_skills_on_profile']);
        });

        Schema::table('hackatons', function (Blueprint $table) {
            $table->dropColumn('is_results_public');
        });
    }
};
