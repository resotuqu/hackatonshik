<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 32)->default('user')->change();
            $table->boolean('is_profile_public')->default(true)->after('description');
            $table->boolean('show_email_on_profile')->default(false)->after('is_profile_public');
            $table->boolean('show_phone_on_profile')->default(false)->after('show_email_on_profile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'is_profile_public',
                'show_email_on_profile',
                'show_phone_on_profile',
            ]);
        });
    }
};
