<?php

use App\Enums\JudgeDomain;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackaton_judges', function (Blueprint $table): void {
            $table
                ->string('domain', 32)
                ->default(JudgeDomain::DEV->value)
                ->after('assigned_at');
            $table->index(['hackaton_id', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_judges', function (Blueprint $table): void {
            $table->dropIndex(['hackaton_id', 'domain']);
            $table->dropColumn('domain');
        });
    }
};
