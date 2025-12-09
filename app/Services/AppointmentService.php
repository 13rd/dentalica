<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{

    public function createAppointment($patientId, Schedule $schedule, array $serviceIds = []): Appointment
    {
        if (!$schedule->is_available || $schedule->appointment()->exists()) {
            throw new \Exception('Этот слот уже занят.');
        }

        $basePrice = 2500;
        $servicesPrice = 0;

        if (!empty($serviceIds)) {
            $services = Service::findMany($serviceIds);
            $servicesPrice = $services->sum('price');
        }

        $totalPrice = $basePrice + $servicesPrice;

        return DB::transaction(function () use ($schedule, $patientId, $totalPrice, $serviceIds) {

            $appointment = $schedule->doctor->appointments()->create([
                'patient_id'     => $patientId,
                'schedule_id'    => $schedule->id,
                'status'         => 'confirmed',
                'payment_status' => 'pending',
                'base_price'     => 2500,
                'total_price'    => $totalPrice,
                'expires_at'     => now()->addMinutes(10),
            ]);


            if (!empty($serviceIds)) {
                $appointment->services()->sync($serviceIds);
            }


            $schedule->update(['is_available' => false]);

            return $appointment;
        });
    }


    public function processPayment(Appointment $appointment): void
    {
        if ($appointment->payment_status !== 'pending') {
            throw new \Exception('Запись уже обработана.');
        }

        if (now()->greaterThan($appointment->expires_at)) {
            $this->cancelAppointment($appointment);
            throw new \Exception('Время оплаты истекло.');
        }

        $appointment->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
            'expires_at'     => null,
        ]);
    }


    public function cancelByPatient(Appointment $appointment): void
    {
        if (!in_array($appointment->status, ['confirmed', 'pending'])) {
            throw new \Exception('Эту запись нельзя отменить.');
        }

        $this->cancelAppointment($appointment);
    }


    public function cancelByDoctor(Appointment $appointment): void
    {
        $this->cancelAppointment($appointment);
    }


    public function completeAppointment(Appointment $appointment): void
    {
        if ($appointment->payment_status !== 'paid') {
            throw new \Exception('Нельзя завершить неоплаченный приём.');
        }

        $appointment->update(['status' => 'completed']);
    }


    private function cancelAppointment(Appointment $appointment): void
    {

        $appointment->schedule()->update(['is_available' => true]);
        $appointment->delete();
    }
}
