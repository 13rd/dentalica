<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateSchedules;
use App\Mail\AppointmentCreated;
use App\Services\AppointmentService;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
        $this->middleware('auth');
        $this->middleware('role:patient')->except(['index', 'show', 'complete']);
        $this->middleware('role:doctor')->only(['complete']);
    }

        public function create(Doctor $doctor)
    {

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



public function store(Request $request, Doctor $doctor)
{
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
        'service_ids' => 'nullable|array',
        'service_ids.*' => 'exists:services,id',
    ]);

    $schedule = Schedule::findOrFail($request->schedule_id);

    try {
        $this->appointmentService->createAppointment(
            auth()->id(),
            $schedule,
            $request->service_ids ?? []
        );
    }catch (\Exception $e){
        return back()->withErrors(['schedule_id' => $e->getMessage()]);
    }

    return redirect()->route('patient.dashboard')->with('success', 'Вы записаны! Оплатите в течении 10 минут.');


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

    $request->validate([
        'send_notification' => 'nullable|boolean'
    ]);

    try {
        $this->appointmentService->processPayment($appointment, $request->boolean('send_notification'));

    } catch (Exception $e) {
        return redirect()->route('patient.dashboard')->with('error', $e->getMessage());
    }

    return redirect()->route('patient.dashboard')
        ->with('success', 'Оплата прошла успешно! Ждём вас на приёме.');
}

    public function cancel(Appointment $appointment)
{
    if ($appointment->patient_id !== auth()->id()) {
        abort(403);
    }

    try {
        $this->appointmentService->cancelByPatient($appointment);
    } catch (Exception $e){
        return back()->with('error', $e->getMessage());
    }


    return back()->with('success', 'Запись отменена.');
}

    public function complete(Appointment $appointment)
    {

        $this->appointmentService->completeAppointment($appointment);


        return back()->with('success', 'Приём завершён');
    }
};
