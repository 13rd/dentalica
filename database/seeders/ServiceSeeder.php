<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Профессиональная гигиена (AirFlow + ультразвук)', 'price' => 4500],
            ['name' => 'Лечение кариеса (пломба композитная)', 'price' => 6500],
            ['name' => 'Отбеливание Zoom 4', 'price' => 25000],
            ['name' => 'Керамическая коронка E.max', 'price' => 35000],
            ['name' => 'Имплант Osstem (Ю.Корея) + коронка', 'price' => 75000],
            ['name' => 'Брекеты металлические (2 челюсти)', 'price' => 180000],
            ['name' => 'Удаление зуба простое', 'price' => 3500],
            ['name' => 'Удаление зуба мудрости сложное', 'price' => 9500],
        ];

        $createdServices = [];
        foreach ($services as $service) {
            $createdServices[] = Service::updateOrCreate(
                ['name' => $service['name']],
                ['price' => $service['price']]
            );
        }

        // Assign services to doctors based on their specializations
        $doctors = Doctor::with('specialization')->get();

        foreach ($doctors as $doctor) {
            $servicesToAssign = [];

            switch ($doctor->specialization->name) {
                case 'Терапевт':
                    // Therapist gets general dental services
                    $servicesToAssign = [0, 1, 2, 3]; // hygiene, caries treatment, whitening, crowns
                    break;
                case 'Ортодонт':
                    // Orthodontist gets orthodontic services
                    $servicesToAssign = [5]; // braces
                    break;
                case 'Хирург-имплантолог':
                    // Surgeon gets surgical services
                    $servicesToAssign = [4, 6, 7]; // implants, simple extraction, complex extraction
                    break;
            }

            foreach ($servicesToAssign as $index) {
                if (isset($createdServices[$index])) {
                    $doctor->services()->attach($createdServices[$index]->id);
                }
            }
        }
    }
}
