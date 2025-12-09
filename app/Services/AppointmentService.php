<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\Service;
use App\Mail\AppointmentCreated;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AppointmentService
{

    public function createAppointment($patientId, Schedule $schedule, array $serviceIds = []): Appointment
    {
        // Проверяем доступность слота: должен быть доступен И не иметь активных записей
        $hasActiveAppointment = $schedule->appointment()
            ->whereNotIn('status', ['cancelled'])
            ->whereNull('deleted_at')
            ->exists();

        if (!$schedule->is_available || $hasActiveAppointment) {
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


    public function processPayment(Appointment $appointment, bool $sendNotification = false): void
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

        // Send notification if requested
        if ($sendNotification) {
            Mail::to($appointment->patient->email)->send(new AppointmentCreated($appointment));
        }
    }


    public function cancelByPatient(Appointment $appointment): void
    {
        if (!in_array($appointment->status, ['confirmed', 'pending'])) {
            throw new \Exception('Эту запись нельзя отменить.');
        }

        $this->cancelAppointment($appointment, false); // Keep record for patient cancellations
    }


    public function cancelByDoctor(Appointment $appointment): void
    {
        $this->cancelAppointment($appointment, false); // Keep record for doctor cancellations
    }

    public function cancelExpiredAppointment(Appointment $appointment): void
    {
        $this->cancelAppointment($appointment, true); // Delete expired appointments
    }

    public function completeAppointment(Appointment $appointment): void
    {
        if ($appointment->payment_status !== 'paid') {
            throw new \Exception('Нельзя завершить неоплаченный приём.');
        }

        $appointment->update(['status' => 'completed']);
    }


    private function cancelAppointment(Appointment $appointment, bool $delete = false): void
    {
        // Освобождаем слот
        $appointment->schedule()->update(['is_available' => true]);

        if ($delete) {
            // Удаляем запись полностью (для истекших по времени оплаты)
            $appointment->delete();
        } else {
            // Помечаем как отменённую (для ручной отмены)
            $appointment->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled',
                'expires_at' => null,
            ]);
        }
    }
}
