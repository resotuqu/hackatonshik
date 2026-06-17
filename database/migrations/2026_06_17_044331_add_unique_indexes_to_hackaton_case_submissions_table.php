<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['pgsql', 'sqlite'], true)) {
            DB::statement(
                'CREATE UNIQUE INDEX hackaton_case_submissions_team_unique ON hackaton_case_submissions (hackaton_case_id, team_id) WHERE team_id IS NOT NULL'
            );
            DB::statement(
                'CREATE UNIQUE INDEX hackaton_case_submissions_user_unique ON hackaton_case_submissions (hackaton_case_id, user_id) WHERE user_id IS NOT NULL'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['pgsql', 'sqlite'], true)) {
            DB::statement('DROP INDEX IF EXISTS hackaton_case_submissions_team_unique');
            DB::statement('DROP INDEX IF EXISTS hackaton_case_submissions_user_unique');
        }
    }
};
