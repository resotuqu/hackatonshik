<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('nickname')->nullable()->change();
            $table->string('oauth_provider')->nullable()->after('remember_token');
            $table->string('oauth_provider_id')->nullable()->after('oauth_provider');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('nickname')->nullable(false)->change();
            $table->dropColumn(['oauth_provider', 'oauth_provider_id']);
        });
    }
};
