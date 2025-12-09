<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredAppointments extends Command
{
    protected $signature = 'appointments:cancel-expired';
    protected $description = 'Отменяет неоплаченные записи через 10 минут';

    public function __construct(protected AppointmentService $appointmentService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $expired = Appointment::where('payment_status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with('schedule')
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Нет просроченных записей.');
            return 0;
        }

        foreach ($expired as $appointment) {
            // Используем сервис для отмены с удалением записи
            $this->appointmentService->cancelExpiredAppointment($appointment);

            $this->info("Запись #{$appointment->id} отменена (время оплаты истекло)");
            Log::info("Автоотмена записи ID: {$appointment->id} для пациента {$appointment->patient_id}");
        }

        $this->info("Обработано записей: {$expired->count()}");

        return 0;
    }
}
