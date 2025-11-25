<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        Specialization::create(['name' => 'Orthodontist']);
        Specialization::create(['name' => 'Surgeon']);
        // Add more
    }
};
