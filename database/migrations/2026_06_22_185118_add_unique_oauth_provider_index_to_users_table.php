<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX users_oauth_provider_unique ON users (oauth_provider, oauth_provider_id) WHERE oauth_provider IS NOT NULL AND oauth_provider_id IS NOT NULL');
        } else {
            Schema::table('users', function (Blueprint $table): void {
                $table->unique(['oauth_provider', 'oauth_provider_id'], 'users_oauth_provider_unique');
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_oauth_provider_unique');
        } else {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique('users_oauth_provider_unique');
            });
        }
    }
};
