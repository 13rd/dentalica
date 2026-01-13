@extends('layouts.app')

@section('title', 'Редактирование временного слота')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        Редактирование слота
                        <small class="text-white-50">
                            {{ \Carbon\Carbon::parse($schedule->date)->format('d.m.Y') }}
                        </small>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('doctor.schedule.update', $schedule) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="time_slot" class="form-label fw-bold">Время слота</label>
                            <input type="time" 
                                id="time_slot" 
                                name="time_slot" 
                                class="form-control" 
                                value="{{ \Carbon\Carbon::parse($schedule->time_slot)->format('H:i') }}"
                                required>
                            @error('time_slot')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    id="is_available" 
                                    name="is_available" 
                                    value="1"
                                    {{ $schedule->is_available ? 'checked' : '' }}
                                    @if($schedule->appointment && $schedule->appointment->status !== 'cancelled') disabled @endif>
                                <label class="form-check-label" for="is_available">
                                    Слот доступен для записи
                                </label>
                            </div>
                            @if($schedule->appointment && $schedule->appointment->status !== 'cancelled')
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Нельзя изменить доступность слота с активной записью
                                </small>
                            @endif
                            @error('is_available')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($schedule->appointment && $schedule->appointment->status !== 'cancelled')
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading">Информация о записи:</h6>
                                <p class="mb-1">
                                    <strong>Пациент:</strong> {{ $schedule->appointment->patient->name }}
                                </p>
                                <p class="mb-0">
                                    <strong>Статус:</strong> 
                                    <span class="badge bg-{{ $schedule->appointment->status === 'confirmed' ? 'warning' : 'success' }}">
                                        {{ $schedule->appointment->status === 'confirmed' ? 'Подтверждена' : 'Завершена' }}
                                    </span>
                                </p>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('doctor.schedule') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Назад к расписанию
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
