<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedulePreference;
use Illuminate\Database\Seeder;

class DoctorSchedulePreferencesSeeder extends Seeder
{
    public function run()
    {
        $doctors = Doctor::all();

        foreach ($doctors as $doctor) {
            DoctorSchedulePreference::firstOrCreate([
                'doctor_id' => $doctor->id,
            ], [
                'working_hours' => [
                    'monday' => ['start' => '09:00', 'end' => '18:00'],
                    'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                    'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                    'thursday' => ['start' => '09:00', 'end' => '18:00'],
                    'friday' => ['start' => '09:00', 'end' => '18:00'],
                    'saturday' => ['start' => '09:00', 'end' => '15:00'],
                    'sunday' => null, // выходной
                ],
                'break_times' => [
                    ['start' => '13:00', 'end' => '14:00'], // обеденный перерыв
                ],
                'appointment_duration' => 60,
                'auto_generate_schedule' => true,
            ]);
        }
    }
}
