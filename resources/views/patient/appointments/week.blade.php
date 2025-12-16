@extends('layouts.app')

@section('title', 'Запись на неделю')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Запись на неделю</h2>
            <p class="text-muted mb-0">Выберите время, затем подтвердите запись</p>
        </div>
        <span class="badge text-bg-light text-secondary fw-semibold">
            {{ $start->format('d.m') }} — {{ $end->format('d.m') }}
        </span>
    </div>

    <!-- Service Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Фильтр по услугам</h5>
            <form method="GET" id="serviceFilterForm">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                            @foreach($allServices as $service)
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input service-filter" type="checkbox"
                                               name="services[]" value="{{ $service->id }}"
                                               id="service_{{ $service->id }}"
                                               {{ in_array($service->id, $selectedServiceIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="service_{{ $service->id }}">
                                            {{ $service->name }}
                                            @if(!is_null($service->price))
                                                <span class="text-muted">({{ number_format($service->price) }} ₽)</span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Применить фильтр
                            </button>
                            @if(!empty($selectedServiceIds))
                                <a href="{{ route('patient.appointments.week') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Сбросить фильтр
                                </a>
                            @endif
                        </div>
                        @if(!empty($selectedServiceIds))
                            <div class="mt-3">
                                <small class="text-muted">
                                    Выбрано услуг: {{ count($selectedServiceIds) }}
                                </small>
                                <div class="mt-2">
                                    <small class="text-info">
                                        <i class="fas fa-info-circle"></i>
                                        Показаны только врачи, предоставляющие выбранные услуги
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($slotsByDate->isEmpty())
        <div class="alert alert-info">
            @if(!empty($selectedServiceIds))
                Нет доступных слотов для выбранных услуг на ближайшие 7 дней.
                <br><small>Попробуйте выбрать другие услуги или сбросить фильтр.</small>
            @else
                Нет доступных слотов на ближайшие 7 дней.
            @endif
        </div>
    @else
        @if(!empty($selectedServiceIds))
            <div class="alert alert-success">
                <small>
                    <i class="fas fa-check-circle"></i>
                    Найдено {{ count($doctors) }} врач(ей), предоставляющих выбранные услуги
                </small>
            </div>
        @endif
    @endif

    <div class="row g-4">
        @for ($i = 0; $i < 7; $i++)
            @php
                $date = now()->addDays($i);
                $dateKey = $date->toDateString();
                $daySlots = $slotsByDate[$dateKey] ?? collect();
                $slotsByTime = $daySlots->groupBy('time_slot');
            @endphp

            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <div class="fw-bold">{{ $date->translatedFormat('d F, l') }}</div>
                        <small class="text-muted">Свободных окон: {{ $daySlots->count() }}</small>
                    </div>
                    <div class="card-body">
                        @forelse($slotsByTime as $time => $slots)
                            @php
                                $slotOptions = $slots->map(fn($slot) => [
                                    'label'       => $slot->doctor->user->name,
                                    'doctor_id'   => $slot->doctor_id,
                                    'schedule_id' => $slot->id,
                                ])->values();
                            @endphp
                            <button
                                type="button"
                                class="btn btn-outline-primary w-100 mb-2 text-start time-slot-btn"
                                data-date="{{ $dateKey }}"
                                data-date-human="{{ $date->translatedFormat('d F, l') }}"
                                data-time="{{ $time }}"
                                data-options='@json($slotOptions)'>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ $time }}</span>
                                    <small class="text-muted">{{ $slots->count() }} врач(ей)</small>
                                </div>
                            </button>
                        @empty
                            <div class="text-muted small">Нет свободных слотов</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>

<!-- Модалка для подтверждения записи -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение записи</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="schedule_id" id="scheduleId">

                    <div class="mb-3">
                        <label class="form-label text-muted">Дата и время</label>
                        <div class="fs-5 fw-semibold" id="slotInfo">—</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Выберите врача</label>
                        <select class="form-select" id="doctorSelect" required></select>
                    </div>

                    <div class="mb-4" id="servicesSection" style="display: none;">
                        <label class="form-label">Услуги (необязательно)</label>
                        <div class="row g-2" id="servicesContainer">
                            <!-- Services will be populated dynamically based on selected doctor -->
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Подтвердить запись</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Service filtering functionality
document.addEventListener('DOMContentLoaded', () => {
    // Handle service filter checkboxes
    const serviceCheckboxes = document.querySelectorAll('.service-filter');

    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            // Auto-submit form when checkbox changes (optional - can be removed if user prefers manual submit)
            // document.getElementById('serviceFilterForm').submit();
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('bookingModal');
    const bookingModal = new bootstrap.Modal(modalElement);
    const bookingForm = document.getElementById('bookingForm');
    const scheduleIdInput = document.getElementById('scheduleId');
    const doctorSelect = document.getElementById('doctorSelect');
    const slotInfo = document.getElementById('slotInfo');
    const servicesSection = document.getElementById('servicesSection');
    const servicesContainer = document.getElementById('servicesContainer');
    const storeRouteTemplate = @json(route('appointments.store', ['doctor' => '__ID__']));
    const servicesByDoctor = @json($servicesByDoctor);

    let currentOptions = [];

    const applyDoctorSelection = (doctorId) => {
        const chosen = currentOptions.find(option => option.doctor_id.toString() === doctorId.toString());
        if (!chosen) {
            return;
        }
        scheduleIdInput.value = chosen.schedule_id;
        bookingForm.action = storeRouteTemplate.replace('__ID__', chosen.doctor_id);

        // Update services section based on selected doctor
        updateServicesForDoctor(doctorId);
    };

    const updateServicesForDoctor = (doctorId) => {
        const doctorServices = servicesByDoctor[doctorId] || [];

        if (doctorServices.length > 0) {
            servicesContainer.innerHTML = '';
            doctorServices.forEach(service => {
                const serviceDiv = document.createElement('div');
                serviceDiv.className = 'col-sm-6';
                serviceDiv.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="service_ids[]" value="${service.id}" id="service_${service.id}">
                        <label class="form-check-label" for="service_${service.id}">
                            ${service.name}
                            ${service.price ? `<span class="text-muted">(${service.price} ₽)</span>` : ''}
                        </label>
                    </div>
                `;
                servicesContainer.appendChild(serviceDiv);
            });
            servicesSection.style.display = 'block';
        } else {
            servicesSection.style.display = 'none';
        }
    };

    document.querySelectorAll('.time-slot-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const options = JSON.parse(button.dataset.options || '[]');
            currentOptions = options;

            // Заполняем селект врачей
            doctorSelect.innerHTML = '';
            options.forEach((option, index) => {
                const opt = document.createElement('option');
                opt.value = option.doctor_id;
                opt.textContent = option.label;
                doctorSelect.appendChild(opt);
                if (index === 0) {
                    applyDoctorSelection(option.doctor_id);
                }
            });

            // Информация о выбранном слоте
            slotInfo.textContent = `${button.dataset.dateHuman} — ${button.dataset.time}`;

            bookingModal.show();
        });
    });

    doctorSelect.addEventListener('change', (event) => {
        applyDoctorSelection(event.target.value);
    });
});
</script>
@endsection

