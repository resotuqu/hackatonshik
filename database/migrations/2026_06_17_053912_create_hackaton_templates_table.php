<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hackaton_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('level')->nullable();
            $table->unsignedInteger('start_offset_days')->default(14);
            $table->unsignedInteger('end_offset_days')->default(16);
            $table->unsignedInteger('registration_deadline_offset_days')->nullable();
            $table->json('default_documents')->nullable();
            $table->json('default_case')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hackaton_templates');
    }
};
