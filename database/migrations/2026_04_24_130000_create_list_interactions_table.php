<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_interactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('list_key', 32);
            $table->string('event_name', 64);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['list_key', 'event_name']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_interactions');
    }
};
