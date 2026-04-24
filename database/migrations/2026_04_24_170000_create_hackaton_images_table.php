<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hackaton_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hackaton_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('alt')->nullable();
            $table->timestamps();

            $table->index(['hackaton_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hackaton_images');
    }
};
