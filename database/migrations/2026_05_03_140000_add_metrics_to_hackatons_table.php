<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->decimal('prize_fund', 12, 2)->nullable()->after('end_at');
            $table->unsignedSmallInteger('prize_places_count')->nullable()->after('prize_fund');
            $table->string('level', 32)->nullable()->index()->after('prize_places_count');
            $table->timestamp('registration_deadline_at')->nullable()->index()->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->dropIndex(['level']);
            $table->dropIndex(['registration_deadline_at']);
            $table->dropColumn([
                'prize_fund',
                'prize_places_count',
                'level',
                'registration_deadline_at',
            ]);
        });
    }
};
