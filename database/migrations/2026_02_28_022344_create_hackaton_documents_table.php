<?php

use App\Models\Team;
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
        Schema::create('hackaton_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Hackaton::class);
            $table->string('name');
            $table->string('description');
            $table->string('file_url');
            $table->boolean('filling_by_team_member');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackaton_documents');
    }
};
