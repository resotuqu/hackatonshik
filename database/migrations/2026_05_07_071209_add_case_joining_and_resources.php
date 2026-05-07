<?php

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
            $table->foreignId('hackaton_case_id')->nullable()->constrained('hackaton_cases')->onDelete('set null');
        });

        Schema::table('hackaton_cases', function (Blueprint $table) {
            $table->text('resources_json')->nullable();
        });

        Schema::create('hackaton_case_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackaton_case_id')->constrained('hackaton_cases')->onDelete('cascade');
            $table->string('path');
            $table->string('alt')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackaton_case_images');

        Schema::table('hackaton_cases', function (Blueprint $table) {
            $table->dropColumn('resources_json');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hackaton_case_id');
        });
    }
};
