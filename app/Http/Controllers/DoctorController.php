<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Specialization;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $specializations = Specialization::all();
        $query = Doctor::with('specialization', 'user');

        if ($request->spec) {
            $query->where('specialization_id', $request->spec);
        }

        $sort = $request->sort ?? 'rating';
        $direction = $request->direction ?? 'desc';
        $doctors = $query->orderBy($sort, $direction)->paginate(10);

        return view('doctors.index', compact('doctors', 'specializations'));
    }

    public function show(Doctor $doctor)
    {
        $schedules = Schedule::where('doctor_id', $doctor->id)
            ->where('is_available', true)
            ->where('date', '>=', now())
            ->orderBy('date')->orderBy('time_slot')
            ->get();

        return view('doctors.show', compact('doctor', 'schedules'));
    }

    // Для доктора: свой дашборд
    public function dashboard()
    {
        $doctor = auth()->user()->doctor;
        $appointments = $doctor->appointments()->with('patient', 'schedule')->get();
        return view('doctor.dashboard', compact('appointments'));
    }

    public function schedule()
    {
        $doctor = auth()->user()->doctor;
        $schedules = $doctor->schedules()->orderBy('date')->get();
        return view('doctor.schedule', compact('schedules'));
    }
};
