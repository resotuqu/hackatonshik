<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hackaton_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hackaton_id')->constrained()->cascadeOnDelete();
            $table->string('message')->nullable();
            $table->string('status')->default(ApplicationStatus::PENDING->value);
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['team_id', 'hackaton_id']); // одна команда — одна заявка на хакатон
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hackaton_applications');
    }
};
