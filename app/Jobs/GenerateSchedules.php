<?php

namespace App\Jobs;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $doctors = Doctor::all();
        $startDate = now()->addDay(); // Tomorrow
        for ($day = 0; $day < 7; $day++) { // Week ahead
            $date = $startDate->clone()->addDays($day);
            if ($date->isWeekend()) continue; // Skip weekends

            foreach ($doctors as $doctor) {
                for ($hour = 9; $hour < 18; $hour++) {
                    $time = sprintf('%02d:00:00', $hour);
                    Schedule::firstOrCreate([
                        'doctor_id' => $doctor->id,
                        'date' => $date->format('Y-m-d'),
                        'time_slot' => $time,
                    ], [
                        'is_available' => true,
                    ]);
                }
            }
        }
    }
};
