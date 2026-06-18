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
        Schema::table('hackaton_watches', function (Blueprint $table): void {
            $table->timestamp('reminder_sent_at')->nullable()->after('hackaton_id');
            $table->index('reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_watches', function (Blueprint $table): void {
            $table->dropIndex(['reminder_sent_at']);
            $table->dropColumn('reminder_sent_at');
        });
    }
};
