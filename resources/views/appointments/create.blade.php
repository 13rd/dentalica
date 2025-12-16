{{-- resources/views/appointments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Запись к ' . $doctor->user->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-1">Запись на приём</h3>
                    <h5 class="mb-0">
                        {{ $doctor->user->name }}
                        <small class="d-block opacity-90">{{ $doctor->specialization->name }}</small>
                    </h5>
                </div>

                <div class="card-body p-5">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('appointments.store', $doctor) }}" id="appointmentForm">
                        @csrf

                        <!-- Выбор времени -->
                        <div class="mb-5">
                            <h5 class="mb-4">Выберите время приёма</h5>

                            @if($schedules->isEmpty())
                                <div class="alert alert-info text-center">
                                    Нет свободных слотов на ближайшие 14 дней.
                                </div>
                            @else
                                <div class="row g-4">
                                    @foreach($schedules as $date => $slots)
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-4 bg-light shadow-sm">
                                                <h6 class="text-primary fw-bold mb-3">
                                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('d F (l)') }}
                                                    @if($date == today()->toDateString())
                                                        <span class="badge bg-danger ms-2">Сегодня</span>
                                                    @endif
                                                </h6>

                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($slots as $slot)
                                                        @php
                                                            $isBooked = !$slot->is_available ||
                                                                        ($slot->appointment && $slot->appointment->status !== 'cancelled');
                                                        @endphp

                                                        @if($isBooked)
                                                            <button type="button" class="btn btn-secondary btn-sm px-3" disabled>
                                                                {{ \Carbon\Carbon::parse($slot->time_slot)->format('H:i') }}
                                                            </button>
                                                        @else
                                                            <label class="btn btn-outline-success btn-sm time-slot-label px-4
                                                                @if(old('schedule_id') == $slot->id) active @endif"
                                                                style="min-width: 85px;">
                                                                <input type="radio" name="schedule_id" value="{{ $slot->id }}"
                                                                       class="d-none" required
                                                                       {{ old('schedule_id') == $slot->id ? 'checked' : '' }}>
                                                                {{ \Carbon\Carbon::parse($slot->time_slot)->format('H:i') }}
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Услуги (необязательно) -->
                        @if($services->isNotEmpty())
                        <div class="mb-5">
                            <h5 class="mb-4 text-success">Дополнительные услуги (по желанию)</h5>
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                @foreach($services as $service)
                                    <div class="col">
                                        <div class="form-check border rounded p-3">
                                            <input class="form-check-input" type="checkbox"
                                                   name="service_ids[]" value="{{ $service->id }}"
                                                   id="service{{ $service->id }}"
                                                   {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex justify-content-between w-100"
                                                   for="service{{ $service->id }}" style="cursor: pointer;">
                                                <span>{{ $service->name }}</span>
                                                <span class="text-success fw-bold">{{ number_format($service->price) }} ₽</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Кнопка записи -->
                        <div class="text-center">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-lg px-5" disabled>
                                Записаться на приём
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-secondary">
                    ← Назад к врачу
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .time-slot-label {
        transition: all 0.2s ease;
        position: relative;
    }
    .time-slot-label:hover {
        background-color: #d1e7dd !important;
        border-color: #0d6efd !important;
    }
    .time-slot-label input:checked ~ * {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }
    .time-slot-label.active {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const submitBtn = document.getElementById('submitBtn');

    function updateButton() {
        const timeSelected = document.querySelector('input[name="schedule_id"]:checked');
        submitBtn.disabled = !timeSelected; // главное — выбрано время
    }

    // Клик по слоту → выбираем радиокнопку
    document.querySelectorAll('.time-slot-label').forEach(label => {
        label.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio && !radio.disabled) {
                radio.checked = true;
                // Убираем active у всех, добавляем к текущему
                document.querySelectorAll('.time-slot-label').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                updateButton();
            }
        });
    });

    // При изменении радиокнопки
    document.querySelectorAll('input[name="schedule_id"]').forEach(radio => {
        radio.addEventListener('change', updateButton);
    });

    // При загрузке (old input)
    updateButton();
});
</script>
@endsection
