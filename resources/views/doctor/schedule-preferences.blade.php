@extends('layouts.app')

@section('title', 'Настройки расписания')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-primary">Настройки расписания</h1>

    <form method="POST" action="{{ route('doctor.schedule-preferences.update') }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Рабочие часы</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(['monday' => 'Понедельник', 'tuesday' => 'Вторник', 'wednesday' => 'Среда', 'thursday' => 'Четверг', 'friday' => 'Пятница', 'saturday' => 'Суббота', 'sunday' => 'Воскресенье'] as $day => $label)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">{{ $label }}</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="time" 
                                                name="working_hours[{{ $day }}][start]" 
                                                class="form-control" 
                                                value="{{ $preferences->working_hours[$day]['start'] ?? '' }}"
                                                placeholder="С">
                                        </div>
                                        <div class="col-6">
                                            <input type="time" 
                                                name="working_hours[{{ $day }}][end]" 
                                                class="form-control" 
                                                value="{{ $preferences->working_hours[$day]['end'] ?? '' }}"
                                                placeholder="По">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Время перерывов</h5>
                    </div>
                    <div class="card-body">
                        <div id="break-times">
                            @foreach($preferences->break_times ?? [] as $index => $break)
                                <div class="row mb-3 break-time-row">
                                    <div class="col-5">
                                        <input type="time" 
                                            name="break_times[{{ $index }}][start]" 
                                            class="form-control" 
                                            value="{{ $break['start'] }}"
                                            placeholder="Начало">
                                    </div>
                                    <div class="col-5">
                                        <input type="time" 
                                            name="break_times[{{ $index }}][end]" 
                                            class="form-control" 
                                            value="{{ $break['end'] }}"
                                            placeholder="Конец">
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeBreakTime(this)">Удалить</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary" onclick="addBreakTime()">Добавить перерыв</button>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Другие настройки</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Продолжительность приема (минут)</label>
                                <input type="number" 
                                    name="appointment_duration" 
                                    class="form-control" 
                                    value="{{ $preferences->appointment_duration }}"
                                    min="15" 
                                    max="240" 
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" 
                                        type="checkbox" 
                                        name="auto_generate_schedule" 
                                        value="1"
                                        {{ $preferences->auto_generate_schedule ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        Автоматически генерировать расписание
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    Сохранить настройки
                </button>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Информация</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">
                            <strong>Рабочие часы:</strong> Укажите время начала и окончания работы для каждого дня недели. 
                            Для выходных дней оставьте поля пустыми.
                        </p>
                        <p class="small text-muted">
                            <strong>Перерывы:</strong> Добавьте время перерывов (обед, технические паузы). 
                            В это время слоты не будут генерироваться.
                        </p>
                        <p class="small text-muted">
                            <strong>Продолжительность приема:</strong> Длительность одного приема в минутах. 
                            По умолчанию 60 минут.
                        </p>
                        <p class="small text-muted">
                            <strong>Автогенерация:</strong> Если включено, система будет автоматически 
                            создавать расписание на основе ваших настроек.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let breakTimeIndex = {{ count($preferences->break_times ?? []) }};

function addBreakTime() {
    const container = document.getElementById('break-times');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-3 break-time-row';
    newRow.innerHTML = `
        <div class="col-5">
            <input type="time" 
                name="break_times[${breakTimeIndex}][start]" 
                class="form-control" 
                placeholder="Начало">
        </div>
        <div class="col-5">
            <input type="time" 
                name="break_times[${breakTimeIndex}][end]" 
                class="form-control" 
                placeholder="Конец">
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeBreakTime(this)">Удалить</button>
        </div>
    `;
    container.appendChild(newRow);
    breakTimeIndex++;
}

function removeBreakTime(button) {
    button.closest('.break-time-row').remove();
}
</script>
@endsection
