<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedulePreference;
use Illuminate\Http\Request;

class DoctorSchedulePreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function edit()
    {
        $doctor = auth()->user()->doctor;
        $preferences = $doctor->schedulePreferences ?? new DoctorSchedulePreference([
            'doctor_id' => $doctor->id,
            'working_hours' => (new DoctorSchedulePreference())->getDefaultWorkingHours(),
            'break_times' => [],
            'appointment_duration' => 60,
            'auto_generate_schedule' => true,
        ]);

        return view('doctor.schedule-preferences', compact('preferences'));
    }

    public function update(Request $request)
    {
        $doctor = auth()->user()->doctor;
        
        $validated = $request->validate([
            'working_hours' => 'required|array',
            'working_hours.*.start' => 'required|date_format:H:i',
            'working_hours.*.end' => 'required|date_format:H:i|after:working_hours.*.start',
            'break_times' => 'nullable|array',
            'break_times.*.start' => 'required|date_format:H:i',
            'break_times.*.end' => 'required|date_format:H:i|after:break_times.*.start',
            'appointment_duration' => 'required|integer|min:15|max:240',
            'auto_generate_schedule' => 'boolean',
        ]);

        $doctor->schedulePreferences()->updateOrCreate(
            ['doctor_id' => $doctor->id],
            $validated
        );

        return back()->with('success', 'Настройки расписания сохранены');
    }
}
