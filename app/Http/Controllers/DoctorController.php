<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Specialization;
use Illuminate\Http\Request;
use App\Models\Appointment;
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

    /* public function __construct() */
    /* { */
    /*     $this->middleware('auth'); */
    /*     $this->middleware('role:doctor'); */
    /* } */
    public function createSchedule(Request $request)
    {
        $doctor = auth()->user()->doctor;

        // Если пришёл POST — сохраняем
        if ($request->isMethod('post')) {
            $request->validate([
                'date'       => 'required|date|after_or_equal:today',
                'time_slots' => 'required|array',
                'time_slots.*' => 'date_format:H:i',
            ]);

            foreach ($request->time_slots as $time) {
                Schedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'date'      => $request->date,
                        'time_slot' => $time . ':00', // если храним как time
                    ],
                    [
                        'is_available' => true,
                    ]
                );
            }

            return back()->with('success', 'Расписание на ' . \Carbon\Carbon::parse($request->date)->format('d.m.Y') . ' успешно создано!');
        }

        return view('doctor.create_schedule', compact('doctor'));
    }
    // Главная страница врача — его расписание и пациенты
    public function dashboard()
    {
        $doctor = auth()->user()->doctor;

        // Сегодняшние приёмы
        $todayAppointments = $doctor->appointments()
            ->with(['patient', 'schedule', 'services'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', today())->where('payment_status', 'paid')->whereIn('status', ['confirmed', 'completed']))
            ->orderBy(Schedule::select('time_slot')->whereColumn('schedules.id', 'appointments.schedule_id'))
            ->get();

        // Все будущие приёмы (на ближайшие 30 дней)
        $upcomingAppointments = $doctor->appointments()
            ->with(['patient', 'schedule'])
            ->whereHas('schedule', fn($q) => $q->where('date', '>', today()))
            ->orderBy(Schedule::select('date')->whereColumn('schedules.id', 'appointments.schedule_id'))
            ->orderBy(Schedule::select('time_slot')->whereColumn('schedules.id', 'appointments.schedule_id'))
            ->get();

        // Все слоты врача на ближайшие 14 дней (для отображения расписания)
        $schedules = Schedule::with(['appointment.patient'])
            ->where('doctor_id', $doctor->id)
            ->whereBetween('date', [today(), today()->addDays(14)])
            ->orderBy('date')
            ->orderBy('time_slot')
            ->get()
            ->groupBy('date');

        return view('doctor.dashboard', compact(
            'doctor',
            'todayAppointments',
            'upcomingAppointments',
            'schedules'
        ));
    }

    // Страница со всеми слотами врача (для просмотра и редактирования)
    public function schedule()
{
    $doctor = auth()->user()->doctor;

    $schedules = \App\Models\Schedule::with([
        'appointment.patient.user',
        'appointment.services'
    ])
    ->where('doctor_id', $doctor->id)
    ->where('date', '>=', now()->subDay())
    ->whereHas('appointment', function ($q) {
        // Показываем ТОЛЬКО записи, где:
        // — оплачено
        // — или статус completed (уже был приём)
        // — или cancelled (чтобы врач видел, что кто-то отменил)
        $q->where('payment_status', 'paid')->whereIn('status', ['confirmed', 'completed']);
    })
    ->orWhere('is_available', true) // плюс все свободные слоты
    ->orderBy('date')
    ->orderBy('time_slot')
    ->get()
    ->groupBy(fn($item) => $item->date->format('Y-m-d'));

    return view('doctor.schedule', compact('schedules'));
}

    // Статус: завершить приём
    public function cancel(Appointment $appointment)
{
    if ($appointment->doctor_id !== auth()->user()->doctor->id) {
        abort(403);
    }

    $appointment->update([
        'status'         => 'cancelled',
        'payment_status' => 'cancelled',
    ]);

    // Освобождаем слот
    $appointment->schedule->update(['is_available' => true]);

    return back()->with('success', 'Запись отменена. Слот снова доступен.');
}

public function complete(Appointment $appointment)
{
    if ($appointment->doctor_id !== auth()->user()->doctor->id) {
        abort(403);
    }

    if ($appointment->payment_status !== 'paid') {
        return back()->with('error', 'Нельзя завершить неоплаченный приём');
    }

    $appointment->update([
        'status' => 'completed'
    ]);

    return back()->with('success', 'Приём успешно завершён!');
}
};
