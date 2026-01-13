<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedulePreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'working_hours',
        'break_times',
        'appointment_duration',
        'auto_generate_schedule',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'break_times' => 'array',
        'auto_generate_schedule' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getDefaultWorkingHours()
    {
        return [
            'monday' => ['start' => '09:00', 'end' => '18:00'],
            'tuesday' => ['start' => '09:00', 'end' => '18:00'],
            'wednesday' => ['start' => '09:00', 'end' => '18:00'],
            'thursday' => ['start' => '09:00', 'end' => '18:00'],
            'friday' => ['start' => '09:00', 'end' => '18:00'],
            'saturday' => ['start' => '09:00', 'end' => '15:00'],
            'sunday' => null, // выходной
        ];
    }
}
