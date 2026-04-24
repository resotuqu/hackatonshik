<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hackaton_case_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hackaton_case_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('score');
            $table->unsignedSmallInteger('max_score')->default(100);
            $table->text('comment')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();

            $table->unique('hackaton_case_submission_id');
            $table->index(['reviewed_by', 'reviewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hackaton_case_scores');
    }
};
