<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hackaton_templates', function (Blueprint $table) {
            $table->string('locale', 8)->default('ru')->after('description');
            $table->unsignedInteger('version')->default(1)->after('locale');
            $table->boolean('is_public')->default(false)->after('is_active');
            $table->string('cover_image_path')->nullable()->after('is_public');
            $table->timestamp('published_at')->nullable()->after('cover_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('hackaton_templates', function (Blueprint $table) {
            $table->dropColumn([
                'locale',
                'version',
                'is_public',
                'cover_image_path',
                'published_at',
            ]);
        });
    }
};
