<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateSchedules;
use App\Mail\AppointmentCreated;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Doctor $doctor)
    {
        $schedules = Schedule::where('doctor_id', $doctor->id)->where('is_available', true)->get();
        $services = Service::all();
        return view('appointments.create', compact('doctor', 'schedules', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'schedule_id' => 'required|exists:schedules,id',
            'services' => 'array',
            'services.*' => 'exists:services,id',
        ]);

        $schedule = Schedule::findOrFail($validated['schedule_id']);
        if (!$schedule->is_available) {
            return back()->withErrors('Slot is not available');
        }

        $appointment = Appointment::create([
            'patient_id' => auth()->id(),
            'doctor_id' => $validated['doctor_id'],
            'schedule_id' => $validated['schedule_id'],
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        if (isset($validated['services'])) {
            $appointment->services()->attach($validated['services']);
            $appointment->total_price = $appointment->services()->sum('price');
        } else {
            $appointment->total_price = 0;
        }
        $appointment->save();

        // Mock payment
        sleep(2); // Simulate processing
        $appointment->payment_status = 'paid';
        $appointment->status = 'confirmed';
        $appointment->save();

        $schedule->is_available = false;
        $schedule->save();

        // Send email
        Mail::to(auth()->user())->send(new AppointmentCreated($appointment));

        return redirect()->route('patient.dashboard')->with('success', 'Appointment created');
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->patient_id !== auth()->id() && !auth()->user()->isDoctor() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        $appointment->schedule->is_available = true;
        $appointment->schedule->save();

        // Send cancel email...

        return back()->with('success', 'Cancelled');
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
