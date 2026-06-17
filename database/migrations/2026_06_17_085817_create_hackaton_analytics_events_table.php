<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hackaton_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackaton_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_name');
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['hackaton_id', 'event_name', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hackaton_analytics_events');
    }
};
