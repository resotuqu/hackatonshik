<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->text('value');
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        DB::table('platform_settings')->insert([
            'key' => 'feature.chat_large_files',
            'value' => '1',
            'label' => 'Загрузка файлов до 50 МБ в чате',
            'description' => 'Разрешает участникам команд прикреплять файлы размером до 50 МБ в командном чате. Ранее загруженные файлы остаются доступными при отключении. Требует PHP upload_max_filesize ≥ 50M и post_max_size ≥ 50M.',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
