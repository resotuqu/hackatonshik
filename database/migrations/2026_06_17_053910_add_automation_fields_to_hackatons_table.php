<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->boolean('auto_issue_certificates')->default(false)->after('registration_deadline_at');
            $table->boolean('auto_publish_results_announcement')->default(false)->after('auto_issue_certificates');
            $table->string('certificate_template_path')->nullable()->after('auto_publish_results_announcement');
            $table->timestamp('finished_automations_ran_at')->nullable()->after('certificate_template_path');
        });
    }

    public function down(): void
    {
        Schema::table('hackatons', function (Blueprint $table) {
            $table->dropColumn([
                'auto_issue_certificates',
                'auto_publish_results_announcement',
                'certificate_template_path',
                'finished_automations_ran_at',
            ]);
        });
    }
};
