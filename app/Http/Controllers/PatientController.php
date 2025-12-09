<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

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
public function cancel(Appointment $appointment)
{   //TODO:if's to middleware

    // Проверяем, что запись принадлежит текущему пациенту
    if ($appointment->patient_id !== auth()->id()) {
        abort(403);
    }

    // Нельзя отменять уже оплаченные или давно отменённые
    if ($appointment->payment_status === 'paid') {
        return back()->with('error', 'Оплаченную запись нельзя отменить');
    }

    if ($appointment->status === 'cancelled') {
        return back()->with('info', 'Запись уже отменена');
    }

    // Отменяем запись
    $appointment->update([
        'status'         => 'cancelled',
        'payment_status' => 'cancelled',
        'expires_at'     => null,
    ]);

    // ВАЖНО: освобождаем слот!
    $appointment->schedule->update(['is_available' => true]);

    return back()->with('success', 'Запись успешно отменена. Слот снова доступен для бронирования.');
}
}
