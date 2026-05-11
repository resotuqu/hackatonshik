<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackaton_cases', function (Blueprint $table): void {
            $table->json('rubric_json')->nullable()->after('resources_json');
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_cases', function (Blueprint $table): void {
            $table->dropColumn('rubric_json');
        });
    }
};
