<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackaton_applications', function (Blueprint $table) {
            $table->unsignedSmallInteger('hackaton_cases_count_when_applied')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_applications', function (Blueprint $table) {
            $table->dropColumn('hackaton_cases_count_when_applied');
        });
    }
};
