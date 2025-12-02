<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Добавляем только если колонок нет
            if (!Schema::hasColumn('appointments', 'base_price')) {
                $table->decimal('base_price', 10, 2)->default(2500)->after('status');
            }
            if (!Schema::hasColumn('appointments', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(2500)->after('base_price');
            }
            if (!Schema::hasColumn('appointments', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])
                      ->default('pending')
                      ->after('total_price');
            }
            if (!Schema::hasColumn('appointments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('appointments', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'total_price', 'payment_status', 'paid_at', 'expires_at']);
        });
    }
};
