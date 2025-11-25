<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create(['name' => 'Cleaning', 'price' => 50.00]);
        Service::create(['name' => 'Filling', 'price' => 100.00]);
        // Add more
    }
};
