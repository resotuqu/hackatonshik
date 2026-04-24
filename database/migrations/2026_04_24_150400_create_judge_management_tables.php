<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judge_invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hackaton_id')->constrained()->cascadeOnDelete();
            $table->string('invited_email');
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invited_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token', 64)->unique();
            $table->string('status', 32)->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['hackaton_id', 'status']);
            $table->unique(['hackaton_id', 'invited_email', 'status'], 'judge_invites_unique_pending');
        });

        Schema::create('hackaton_judges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hackaton_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['hackaton_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hackaton_judges');
        Schema::dropIfExists('judge_invitations');
    }
};
