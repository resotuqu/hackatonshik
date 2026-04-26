<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('is_public')->index();
        });

        DB::table('hackatons')
            ->where('is_public', false)
            ->update(['status' => 'draft']);

        DB::table('hackatons')
            ->where('is_public', true)
            ->where('start_at', '>', now())
            ->update(['status' => 'published']);

        DB::table('hackatons')
            ->where('is_public', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->update(['status' => 'in_progress']);

        DB::table('hackatons')
            ->where('is_public', true)
            ->where('end_at', '<', now())
            ->update(['status' => 'finished']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
