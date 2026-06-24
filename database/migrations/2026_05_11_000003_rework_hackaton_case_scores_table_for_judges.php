<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('hackaton_case_scores_new', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hackaton_case_submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();

            $table->unsignedSmallInteger('score');
            $table->unsignedSmallInteger('max_score')->default(100);

            $table->json('criteria_scores')->nullable();
            $table->json('field_comments')->nullable();
            $table->text('general_comment')->nullable();

            $table->boolean('is_final')->default(false);
            $table->timestamp('draft_saved_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->unique(['hackaton_case_submission_id', 'reviewed_by'], 'hackaton_case_scores_unique_submission_judge');
            $table->index(['reviewed_by', 'reviewed_at']);
        });

        DB::statement(<<<'SQL'
            INSERT INTO hackaton_case_scores_new (
                id,
                hackaton_case_submission_id,
                reviewed_by,
                score,
                max_score,
                general_comment,
                is_final,
                reviewed_at,
                created_at,
                updated_at
            )
            SELECT
                id,
                hackaton_case_submission_id,
                reviewed_by,
                score,
                max_score,
                comment,
                true,
                reviewed_at,
                created_at,
                updated_at
            FROM hackaton_case_scores
        SQL);

        Schema::drop('hackaton_case_scores');
        Schema::rename('hackaton_case_scores_new', 'hackaton_case_scores');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('hackaton_case_scores_old', function (Blueprint $table): void {
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

        DB::statement(<<<'SQL'
            INSERT INTO hackaton_case_scores_old (
                id,
                hackaton_case_submission_id,
                reviewed_by,
                score,
                max_score,
                comment,
                reviewed_at,
                created_at,
                updated_at
            )
            SELECT
                id,
                hackaton_case_submission_id,
                reviewed_by,
                score,
                max_score,
                general_comment,
                COALESCE(reviewed_at, created_at),
                created_at,
                updated_at
            FROM hackaton_case_scores
        SQL);

        Schema::drop('hackaton_case_scores');
        Schema::rename('hackaton_case_scores_old', 'hackaton_case_scores');

        Schema::enableForeignKeyConstraints();
    }
};
