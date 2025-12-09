<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Appointment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ScheduleService
{

    public function getDoctorSchedule(Doctor $doctor, bool $includeFreeSlots = true): Collection
    {
        $query = Schedule::with([
            'appointment.patient',
            'appointment.services'
        ])
        ->where('doctor_id', $doctor->id)
        ->where('date', '>=', Carbon::now()->subDay());


        $query->where(function ($q) use ($includeFreeSlots) {
            $q->whereHas('appointment', function ($q) {
                $q->where('payment_status', 'paid')
                  ->whereIn('status', ['confirmed', 'completed']);
            });

            if ($includeFreeSlots) {
                $q->orWhere('is_available', true);
            }
        });

        return $query->orderBy('date')
                     ->orderBy('time_slot')
                     ->get()
                     ->groupBy(fn($item) => \Carbon\Carbon::parse($item->date)->format('Y-m-d'));
    }


    public function getTodayAppointments(Doctor $doctor): Collection
    {
        return $doctor->appointments()
            ->with(['patient', 'schedule', 'services'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', today()))
            ->where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderBy(Schedule::select('time_slot')->whereColumn('schedules.id', 'appointments.schedule_id'))
            ->get();
    }


    public function getUpcomingAppointments(Doctor $doctor, int $days = 30): Collection
    {
        return $doctor->appointments()
            ->with(['patient', 'schedule', 'services'])
            ->whereHas('schedule', fn($q) => $q->where('date', '>', today()))
            ->whereHas('schedule', fn($q) => $q->where('date', '<=', today()->addDays($days)))
            ->where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderBy(Schedule::select('date')->whereColumn('schedules.id', 'appointments.schedule_id'))
            ->orderBy(Schedule::select('time_slot')->whereColumn('schedules.id', 'appointments.schedule_id'))
            ->get();
    }


    public function completeAppointment(Appointment $appointment, Doctor $doctor): bool
    {
        if ($appointment->doctor_id !== $doctor->id) {
            return false;
        }

        if ($appointment->payment_status !== 'paid') {
            return false;
        }

        $appointment->update(['status' => 'completed']);

        return true;
    }


    public function cancelAppointment(Appointment $appointment, Doctor $doctor): bool
    {
        if ($appointment->doctor_id !== $doctor->id) {
            return false;
        }

        $appointment->update([
            'status' => 'cancelled',
            'payment_status' => 'cancelled',
        ]);


        $appointment->schedule?->update(['is_available' => true]);

        return true;
    }
}
