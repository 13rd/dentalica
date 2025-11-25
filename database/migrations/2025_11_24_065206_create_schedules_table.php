<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('time_slot');
            $table->boolean('is_available')->default(true);
            $table->unique(['doctor_id', 'date', 'time_slot']); // Избежать дубликатов
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('schedules'); }
};
