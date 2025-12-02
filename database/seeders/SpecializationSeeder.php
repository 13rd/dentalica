<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            'Терапевт',
            'Ортодонт',
            'Хирург-имплантолог',
            'Ортопед (протезист)',
            'Пародонтолог',
        ];

        foreach ($specializations as $name) {
            Specialization::firstOrCreate(['name' => $name]);
        }
    }
}
