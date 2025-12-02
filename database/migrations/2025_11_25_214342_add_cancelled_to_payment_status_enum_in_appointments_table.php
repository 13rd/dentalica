<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Временно отключаем строгий режим
        DB::statement('SET SESSION sql_mode=""');

        Schema::table('appointments', function (Blueprint $table) {
            // Добавляем cancelled в оба enum
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])
                  ->default('pending')->change();

            $table->enum('status', ['confirmed', 'cancelled'])
                  ->default('confirmed')->change();
        });
    }

    public function down(): void
    {
        // При откате убираем cancelled (осторожно — если есть записи с cancelled, упадёт)
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                  ->default('pending')->change();
            $table->enum('status', ['confirmed'])
                  ->default('confirmed')->change();
        });
    }
};
