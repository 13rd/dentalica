<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctor_schedule_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->json('working_hours')->nullable(); // {"monday": {"start": "09:00", "end": "18:00"}, ...}
            $table->json('break_times')->nullable(); // [{"start": "13:00", "end": "14:00"}]
            $table->integer('appointment_duration')->default(60); // minutes
            $table->boolean('auto_generate_schedule')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_schedule_preferences');
    }
};
