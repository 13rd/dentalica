<?php

namespace App\Console\Commands;

use App\Models\Appointment;        // Это было пропущено!
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredAppointments extends Command
{
    protected $signature = 'appointments:cancel-expired';
    protected $description = 'Отменяет неоплаченные записи через 10 минут';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $expired = Appointment::where('payment_status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with('schedule') // чтобы сразу загрузить слот
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Нет просроченных записей.');
            return 0;
        }

        foreach ($expired as $appointment) {
            // Отменяем запись
            $appointment->update([
                'payment_status' => 'cancelled',
                'status' => 'cancelled',
                'expires_at' => null,
            ]);

            // Освобождаем слот
            if ($appointment->schedule) {
                $appointment->schedule->update(['is_available' => true]);
            }

            $this->info("Запись #{$appointment->id} отменена (время оплаты истекло)");
            Log::info("Автоотмена записи ID: {$appointment->id} для пациента {$appointment->patient_id}");
        }

        $this->info("Обработано записей: {$expired->count()}");

        return 0;
    }
}
