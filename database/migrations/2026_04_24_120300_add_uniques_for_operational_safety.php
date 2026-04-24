<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackaton_certificates', function (Blueprint $table): void {
            $table->unique(['hackaton_id', 'user_id', 'title'], 'hackaton_certificates_unique_title_per_user');
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_certificates', function (Blueprint $table): void {
            $table->dropUnique('hackaton_certificates_unique_title_per_user');
        });
    }
};
