<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'schedule_id',
        'status',
        'base_price',
        'total_price',
        'payment_status',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'paid_at'     => 'datetime',
        'expires_at'  => 'datetime',
        'base_price'  => 'decimal:2',
        'total_price' => 'decimal:2',

        // ← ВОТ ЭТА СТРОКА РЕШАЕТ ВСЁ!
        'payment_status' => 'string',
        'status'         => 'string',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
};
