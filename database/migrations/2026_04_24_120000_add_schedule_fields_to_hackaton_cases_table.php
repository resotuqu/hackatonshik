<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackaton_cases', function (Blueprint $table): void {
            $table->timestamp('publish_at')->nullable()->after('is_published');
            $table->timestamp('deadline_at')->nullable()->after('publish_at');
            $table->index(['hackaton_id', 'publish_at']);
            $table->index(['hackaton_id', 'deadline_at']);
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_cases', function (Blueprint $table): void {
            $table->dropIndex(['hackaton_id', 'publish_at']);
            $table->dropIndex(['hackaton_id', 'deadline_at']);
            $table->dropColumn(['publish_at', 'deadline_at']);
        });
    }
};
