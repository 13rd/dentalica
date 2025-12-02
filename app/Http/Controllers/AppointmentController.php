<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateSchedules;
use App\Mail\AppointmentCreated;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient')->except(['index', 'show']);
    }

    // Показать форму записи к конкретному врачу
    public function create(Doctor $doctor)
    {
        // Загружаем свободные слоты на ближайшие 14 дней
        $schedules = Schedule::where('doctor_id', $doctor->id)
            ->where('is_available', true)
            ->where('date', '>=', today())
            ->where('date', '<=', today()->addDays(14))
            ->orderBy('date')
            ->orderBy('time_slot')
            ->get()
            ->groupBy('date');

        $services = Service::all();

        return view('appointments.create', compact('doctor', 'schedules', 'services'));
    }

    // app/Http/Controllers/AppointmentController.php

public function store(Request $request, Doctor $doctor)
{
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
        'service_ids' => 'nullable|array',
        'service_ids.*' => 'exists:services,id',
    ]);

    $schedule = Schedule::findOrFail($request->schedule_id);

    if ($schedule->doctor_id !== $doctor->id || !$schedule->is_available) {
        return back()->withErrors(['schedule_id' => 'Это время уже занято.']);
    }

    $basePrice = 2500; // базовая стоимость приёма
    $servicesPrice = 0;

    if ($request->filled('service_ids')) {
        $services = Service::find($request->service_ids);
        $servicesPrice = $services->sum('price');
    }

    $totalPrice = $basePrice + $servicesPrice;

    $appointment = $doctor->appointments()->create([
        'patient_id'      => auth()->id(),
        'schedule_id'     => $schedule->id,
        'status'          => 'confirmed',
        'base_price'      => $basePrice,
        'total_price'     => $totalPrice,
        'payment_status'  => 'pending',
        'expires_at'      => now()->addMinutes(10),
    ]);

    if ($request->filled('service_ids')) {
        $appointment->services()->sync($request->service_ids);
    }

    $schedule->update(['is_available' => false]);

    return redirect()->route('patient.dashboard')
        ->with('success', 'Вы записаны! Оплатите приём в течение 10 минут, иначе запись будет отменена.');
}
    public function pay(Appointment $appointment)
{
    if ($appointment->patient_id !== auth()->id() || $appointment->payment_status !== 'pending') {
        abort(403);
    }

    if (now()->greaterThan($appointment->expires_at)) {
        $appointment->update([
            'payment_status' => 'cancelled',
            'status' => 'cancelled'
        ]);
        $appointment->schedule->update(['is_available' => true]);
        return redirect()->route('patient.dashboard')->with('error', 'Время оплаты истекло.');
    }

    return view('appointments.pay', compact('appointment'));
}

public function processPayment(Request $request, Appointment $appointment)
{
    if ($appointment->patient_id !== auth()->id() || $appointment->payment_status !== 'pending') {
        abort(403);
    }

    // Мок оплаты — всегда успешно
    $appointment->update([
        'payment_status' => 'paid',
        'paid_at' => now(),
        'expires_at' => null,
    ]);

    return redirect()->route('patient.dashboard')
        ->with('success', 'Оплата прошла успешно! Ждём вас на приёме.');
}

    public function cancel(Appointment $appointment)
{
    if ($appointment->patient_id !== auth()->id()) {
        abort(403);
    }

    if (!in_array($appointment->status, ['confirmed', 'pending'])) {
        return back()->with('error', 'Эту запись нельзя отменить');
    }

    // Отменяем
    $appointment->update([
        'status'         => 'cancelled',
        'payment_status' => 'cancelled',
        'expires_at'     => null,
    ]);

    // Освобождаем слот
    $appointment->schedule->update(['is_available' => true]);

    return back()->with('success', 'Запись отменена. Слот снова доступен для бронирования.');
}

    public function complete(Appointment $appointment)
    {
        if (!auth()->user()->isDoctor() || $appointment->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        $appointment->status = 'completed';
        $appointment->save();

        return back()->with('success', 'Completed');
    }
};
