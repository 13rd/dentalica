<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function dashboard()
    {
        $user = auth()->user();

        // Get today's appointments (paid or with active timer)
        $todaysPaidAppointments = $user->appointments()
            ->with(['doctor.user', 'schedule', 'services'])
            ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->where('schedules.date', today())
            ->where(function($query) {
                $query->where('appointments.payment_status', 'paid')
                      ->orWhere(function($q) {
                          $q->where('appointments.payment_status', 'pending')
                            ->whereNotNull('appointments.expires_at')
                            ->where('appointments.expires_at', '>', now());
                      });
            })
            ->orderBy('schedules.date')
            ->orderBy('schedules.time_slot')
            ->select('appointments.*')
            ->get();

        // Get other appointments (not today's paid ones)
        $otherAppointments = $user->appointments()
            ->with(['doctor.user', 'schedule', 'services'])
            ->where(function($query) {
                $query->where('payment_status', '!=', 'paid')
                      ->orWhereHas('schedule', function($subQuery) {
                          $subQuery->where('date', '!=', today());
                      });
            })
            ->latest()
            ->get();

        return view('patient.dashboard', compact('todaysPaidAppointments', 'otherAppointments'));
    }

    public function profile()
    {
        return view('patient.profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Профиль обновлён');
    }
    public function weekAppointments(Request $request)
    {
        $start = Carbon::today();
        $end = Carbon::today()->addDays(6);

        $selectedServiceIds = $request->get('services', []);

        // Get all available slots first
        $query = Schedule::with(['doctor.user', 'doctor.services'])
            ->where('is_available', true)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);

        // Filter by selected services if any are chosen
        if (!empty($selectedServiceIds)) {
            $query->whereHas('doctor.services', function ($q) use ($selectedServiceIds) {
                $q->whereIn('services.id', $selectedServiceIds);
            });
        }

        $slotsByDate = $query->orderBy('date')
            ->orderBy('time_slot')
            ->get()
            ->groupBy(function ($slot) {
                return Carbon::parse($slot->date)->toDateString();
            });

        // Group services by doctor for the weekly view
        $servicesByDoctor = [];
        $doctors = [];

        foreach ($slotsByDate as $date => $slots) {
            foreach ($slots as $slot) {
                $doctorId = $slot->doctor_id;
                if (!isset($servicesByDoctor[$doctorId])) {
                    $servicesByDoctor[$doctorId] = $slot->doctor->services->toArray();
                    $doctors[$doctorId] = $slot->doctor;
                }
            }
        }

        // Get all available services for the filter
        $allServices = collect();
        foreach ($doctors as $doctor) {
            $allServices = $allServices->merge($doctor->services);
        }
        $allServices = $allServices->unique('id')->sortBy('name');

        return view('patient.appointments.week', [
            'slotsByDate' => $slotsByDate,
            'servicesByDoctor' => $servicesByDoctor,
            'doctors' => $doctors,
            'allServices' => $allServices,
            'selectedServiceIds' => $selectedServiceIds,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function cancel(Appointment $appointment)
    {   //TODO:if's to middleware

    // Проверяем, что запись принадлежит текущему пациенту
    if ($appointment->patient_id !== auth()->id()) {
        abort(403);
    }

    // Нельзя отменять давно отменённые
    if ($appointment->status === 'cancelled') {
        return back()->with('info', 'Запись уже отменена');
    }

    // Нельзя отменять завершенные приёмы
    if ($appointment->status === 'completed') {
        return back()->with('error', 'Завершённый приём нельзя отменить');
    }

    // Отменяем запись
    $appointment->update([
        'status'         => 'cancelled',
        'payment_status' => 'cancelled',
        'expires_at'     => null,
    ]);

    // ВАЖНО: освобождаем слот!
    $appointment->schedule->update(['is_available' => true]);

    $message = $appointment->payment_status === 'paid'
        ? 'Запись успешно отменена. Деньги будут возвращены на ваш счёт в течение 2 часов.'
        : 'Запись успешно отменена. Слот снова доступен для бронирования.';

    return back()->with('success', $message);
}
}
