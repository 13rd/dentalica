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
        $query = Doctor::with(['specialization', 'user', 'services']);

        // Apply specialization filter
        if ($request->filled('spec')) {
            $query->where('specialization_id', $request->spec);
        }

        // Apply sorting with proper validation
        $sortField = $request->get('sort', 'rating');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['rating', 'name', 'experience'];
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'rating';
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        // For name sorting, we need to join with users table
        if ($sortField === 'name') {
            $query->join('users', 'doctors.user_id', '=', 'users.id')
                  ->orderBy('users.name', $sortDirection)
                  ->select('doctors.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $doctors = $query->paginate(10);

        // Preserve all query parameters in pagination links
        $doctors->appends($request->query());

        return view('doctors.index', compact('doctors', 'specializations'));
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['specialization', 'user', 'services']);
        
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
        $prefilledDate = $request->get('date');

        return view('doctor.create_schedule', compact('doctor', 'prefilledDate'));
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


    public function editTimeSlot(Schedule $schedule)
    {
        if ($schedule->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        return view('doctor.edit-time-slot', compact('schedule'));
    }

    public function updateTimeSlot(Request $request, Schedule $schedule)
    {
        if ($schedule->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'time_slot' => 'required|date_format:H:i',
            'is_available' => 'boolean',
        ]);

        // Проверяем, что слот не пересекается с другими слотами этого врача в тот же день
        $existingSlot = Schedule::where('doctor_id', $schedule->doctor_id)
            ->where('date', $schedule->date)
            ->where('time_slot', $validated['time_slot'] . ':00')
            ->where('id', '!=', $schedule->id)
            ->first();

        if ($existingSlot) {
            return back()->withErrors(['time_slot' => 'На это время уже есть другой слот']);
        }

        // Если слот имеет активную запись, нельзя менять его доступность
        if ($schedule->appointment && $schedule->appointment->status !== 'cancelled') {
            return back()->withErrors(['is_available' => 'Нельзя изменить доступность слота с активной записью']);
        }

        $schedule->update([
            'time_slot' => $validated['time_slot'] . ':00',
            'is_available' => $validated['is_available'] ?? $schedule->is_available,
        ]);

        return redirect()->route('doctor.schedule')
            ->with('success', 'Временной слот обновлён');
    }

    public function deleteTimeSlot(Schedule $schedule)
    {
        if ($schedule->doctor_id !== auth()->user()->doctor->id) {
            abort(403);
        }

        if ($schedule->appointment && $schedule->appointment->status !== 'cancelled') {
            return back()->with('error', 'Нельзя удалить слот с активной записью');
        }

        $schedule->delete();

        return back()->with('success', 'Временной слот удалён');
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
