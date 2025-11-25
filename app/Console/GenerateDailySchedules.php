<?php

namespace App\Console\Commands;

use App\Jobs\GenerateSchedules;
use Illuminate\Console\Command;

class GenerateDailySchedules extends Command
{
    protected $signature = 'schedules:generate';

    protected $description = 'Generate schedules for the next week';

    public function handle()
    {
        GenerateSchedules::dispatch();
        $this->info('Schedules generated');
    }
};
