<?php

namespace App\Jobs;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\DoctorSchedulePreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class GenerateSchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $doctors = Doctor::all();
        $startDate = now()->addDay(); // Tomorrow
        
        for ($day = 0; $day < 14; $day++) { // Two weeks ahead
            $date = $startDate->clone()->addDays($day);
            
            foreach ($doctors as $doctor) {
                // Проверяем, включена ли авто-генерация для врача
                $preferences = $doctor->schedulePreferences;
                
                if ($preferences && !$preferences->auto_generate_schedule) {
                    continue;
                }

                // Получаем рабочие часы для дня недели
                $dayName = strtolower($date->englishDayOfWeek);
                $workingHours = $this->getWorkingHours($doctor, $dayName, $preferences);
                
                if (!$workingHours) {
                    continue; // Врач не работает в этот день
                }

                // Генерируем слоты согласно рабочим часам и продолжительности приема
                $this->generateTimeSlots($doctor, $date, $workingHours, $preferences);
            }
        }
    }

    private function getWorkingHours(Doctor $doctor, string $dayName, ?DoctorSchedulePreference $preferences): ?array
    {
        if ($preferences && isset($preferences->working_hours[$dayName])) {
            return $preferences->working_hours[$dayName];
        }

        // Значения по умолчанию
        $defaultHours = [
            'monday' => ['start' => '09:00', 'end' => '18:00'],
            'tuesday' => ['start' => '09:00', 'end' => '18:00'],
            'wednesday' => ['start' => '09:00', 'end' => '18:00'],
            'thursday' => ['start' => '09:00', 'end' => '18:00'],
            'friday' => ['start' => '09:00', 'end' => '18:00'],
            'saturday' => ['start' => '09:00', 'end' => '15:00'],
            'sunday' => null, // выходной
        ];

        return $defaultHours[$dayName] ?? null;
    }

    private function generateTimeSlots(Doctor $doctor, Carbon $date, array $workingHours, ?DoctorSchedulePreference $preferences)
    {
        $duration = $preferences?->appointment_duration ?? 60; // минут
        $breakTimes = $preferences?->break_times ?? [];

        $startTime = Carbon::parse($workingHours['start']);
        $endTime = Carbon::parse($workingHours['end']);

        $currentTime = $startTime->copy();

        while ($currentTime->lt($endTime)) {
            // Проверяем, не попадает ли текущее время на перерыв
            if ($this->isBreakTime($currentTime, $breakTimes)) {
                $currentTime->addMinutes($duration);
                continue;
            }

            $time = $currentTime->format('H:i:s');
            
            Schedule::firstOrCreate([
                'doctor_id' => $doctor->id,
                'date' => $date->format('Y-m-d'),
                'time_slot' => $time,
            ], [
                'is_available' => true,
            ]);

            $currentTime->addMinutes($duration);
        }
    }

    private function isBreakTime(Carbon $currentTime, array $breakTimes): bool
    {
        foreach ($breakTimes as $break) {
            $breakStart = Carbon::parse($break['start']);
            $breakEnd = Carbon::parse($break['end']);
            
            if ($currentTime->between($breakStart, $breakEnd)) {
                return true;
            }
        }
        
        return false;
    }
};
