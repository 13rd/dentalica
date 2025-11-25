<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SpecializationSeeder::class,
            ServiceSeeder::class,
            // Add UserSeeder, DoctorSeeder etc.
        ]);
    }
};
