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
        $appointments = $user->appointments()
            ->with(['doctor.user', 'schedule', 'services'])
            ->latest()
            ->get();

        return view('patient.dashboard', compact('appointments'));
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
    public function weekAppointments()
    {
        $start = Carbon::today();
        $end = Carbon::today()->addDays(6);

        $slotsByDate = Schedule::with(['doctor.user'])
            ->where('is_available', true)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->orderBy('time_slot')
            ->get()
            ->groupBy(function ($slot) {
                return Carbon::parse($slot->date)->toDateString();
            });

        $services = Service::all();

        return view('patient.appointments.week', [
            'slotsByDate' => $slotsByDate,
            'services'    => $services,
            'start'       => $start,
            'end'         => $end,
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
