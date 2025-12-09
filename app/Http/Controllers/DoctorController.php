<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Specialization;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Services\ScheduleService;
class DoctorController extends Controller
{
    protected ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService){
        $this->scheduleService = $scheduleService;
        $this->middleware('auth');
        $this->middleware('role:doctor')->only([
            'dashboard',
            'schedule',
            'getSchedule',
            'createSchedule',
        ]);
        $this->middleware('role:patient')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $specializations = Specialization::all();
        $query = Doctor::with('specialization', 'user');

        if ($request->spec) {
            $query->where('specialization_id', $request->spec);
        }

        $doctors = $query->orderBy($request->get('sort', 'rating'), $request->get('direction', 'desc'))->paginate(10);





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


    public function getSchedule(Request $request)
    {
        $doctor = auth()->user()->doctor;



        return view('doctor.create_schedule', compact('doctor'));
    }
    public function createSchedule(Request $request){
        $doctor = auth()->user()->doctor;
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
                    'time_slot' => $time . ':00',
                ],
                [
                    'is_available' => true,
                ]
            );
        }

        return back()->with('success', 'Расписание на ' . \Carbon\Carbon::parse($request->date)->format('d.m.Y') . ' успешно создано!');

    }

    public function dashboard()
    {
        $doctor = auth()->user()->doctor;

        $todayAppointments = $this->scheduleService->getTodayAppointments($doctor);
        $upcomingAppointments = $this->scheduleService->getUpcomingAppointments($doctor);
        $schedules = $this->scheduleService->getDoctorSchedule($doctor, includeFreeSlots: true);



        return view('doctor.dashboard', compact(
            'doctor',
            'todayAppointments',
            'upcomingAppointments',
            'schedules'
        ));
    }


    public function schedule()
{
    $doctor = auth()->user()->doctor;

    $schedules = $this->scheduleService->getDoctorSchedule($doctor, includeFreeSlots: true);



    return view('doctor.schedule', compact('schedules'));
}


    public function cancel(Appointment $appointment)
    {
        if ($appointment->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        $success = $this->scheduleService->cancelAppointment($appointment, auth()->user()->doctor);



        return $success ? back()->with('success', 'Запись отменена, слот освобождёт') : back()->with('error', 'Не удалось отменить запись');
    }
/**/
/* public function complete(Appointment $appointment) */
/* { */
/*     if ($appointment->doctor_id !== auth()->user()->doctor->id) { */
/*         abort(403); */
/*     } */
/**/
/**/
/*     $success = $this->scheduleService->completeAppointment($appointment, auth()->user()->doctor); */
/**/
/*     return $success ? back()->with('success', 'Запись отменена, слот освобождёт') : back()->with('error', 'Не удалось отменить запись'); */
/**/
/* } */
};
