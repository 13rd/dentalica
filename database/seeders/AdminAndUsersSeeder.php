<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. Админ ===
        User::updateOrCreate(
            ['email' => 'admin@dental.ru'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '+7 (999) 111-22-33',
            ]
        );

        // === 2. Пациенты ===
        $patients = [
            ['Иван Иванов',     'ivan@mail.ru',     'patient1', '+7 (901) 123-45-67'],
            ['Мария Петрова',   'maria@yandex.ru',  'patient2', '+7 (902) 987-65-43'],
            ['Алексей Сидоров', 'alex@gmail.com',   'patient3', '+7 (903) 555-44-33'],
        ];

        foreach ($patients as $p) {
            User::updateOrCreate(
                ['email' => $p[1]],
                [
                    'name' => $p[0],
                    'password' => Hash::make($p[2]),
                    'role' => 'patient',
                    'phone' => $p[3],
                ]
            );
        }

        // === 3. Врачи ===
        $doctors = [
            ['Анна Смирнова',      'anna@clinic.ru',   'doc1', 'Терапевт',           8],
            ['Дмитрий Козлов',     'dmitry@clinic.ru', 'doc2', 'Ортодонт',          12],
            ['Сергей Волков',      'sergey@clinic.ru', 'doc3', 'Хирург-имплантолог',15],
        ];

        foreach ($doctors as $doc) {
            $user = User::updateOrCreate(
                ['email' => $doc[1]],
                [
                    'name' => $doc[0],
                    'password' => Hash::make($doc[2]),
                    'role' => 'doctor',
                    'phone' => '+7 (999) ' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(10, 99),
                ]
            );

            $specialization = \App\Models\Specialization::where('name', $doc[3])->first();

            Doctor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization_id' => $specialization->id,
                    'bio' => "Врач-стоматолог {$doc[3]}. Стаж {$doc[4]} лет. Постоянно повышаю квалификацию.",
                    'experience_years' => $doc[4],
                    'rating' => number_format(rand(43, 50) / 10, 1), // 4.3 – 5.0
                ]
            );
        }
    }
}
