<?php

namespace Database\Seeders;

use App\Models\Service;
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

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                ['price' => $service['price']]
            );
        }
    }
}
