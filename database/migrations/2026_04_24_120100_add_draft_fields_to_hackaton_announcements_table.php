<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackaton_announcements', function (Blueprint $table): void {
            $table->timestamp('published_at')->nullable()->change();
            $table->boolean('is_draft')->default(false)->after('body');
            $table->string('template_key')->nullable()->after('is_draft');
            $table->index(['hackaton_id', 'is_draft', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_announcements', function (Blueprint $table): void {
            $table->dropIndex(['hackaton_id', 'is_draft', 'published_at']);
            $table->dropColumn(['is_draft', 'template_key']);
            $table->timestamp('published_at')->nullable(false)->change();
        });
    }
};
