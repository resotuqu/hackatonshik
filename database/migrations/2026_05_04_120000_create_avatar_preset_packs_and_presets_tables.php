<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatar_preset_packs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('avatar_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avatar_preset_pack_id')
                ->constrained('avatar_preset_packs')
                ->cascadeOnDelete();
            $table->string('storage_path')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatar_presets');
        Schema::dropIfExists('avatar_preset_packs');
    }
};
