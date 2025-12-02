<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Временно отключаем строгий режим MySQL, чтобы можно было менять enum
        DB::statement('SET SESSION sql_mode=""');

        Schema::table('appointments', function (Blueprint $table) {
            // Добавляем 'completed' в колонку status
            $table->enum('status', ['confirmed', 'cancelled', 'completed'])
                  ->default('confirmed')
                  ->change();

            // На всякий случай добавляем 'completed' и в payment_status (если вдруг захочешь)
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled', 'completed'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down(): void
    {
        // При откате убираем 'completed' (осторожно — если есть записи с completed, упадёт)
        DB::statement('SET SESSION sql_mode=""');

        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('status', ['confirmed', 'cancelled'])
                  ->default('confirmed')
                  ->change();

            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])
                  ->default('pending')
                  ->change();
        });
    }
};
