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
        Schema::create('hackaton_case_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackaton_case_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hackaton_case_field_id')->constrained()->cascadeOnDelete();
            $table->text('value_text')->nullable();
            $table->json('value_json')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->unique(['hackaton_case_submission_id', 'hackaton_case_field_id'], 'hackaton_case_answers_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackaton_case_answers');
    }
};
