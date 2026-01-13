@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Пациент: {{ $user->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.patients.edit', $user) }}" class="btn btn-primary">
                Редактировать пациента
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Основная информация</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Имя:</dt>
                                        <dd class="col-sm-8">{{ $user->name }}</dd>

                                        <dt class="col-sm-4">Email:</dt>
                                        <dd class="col-sm-8">{{ $user->email }}</dd>

                                        <dt class="col-sm-4">Телефон:</dt>
                                        <dd class="col-sm-8">{{ $user->phone ?? 'Не указан' }}</dd>

                                        <dt class="col-sm-4">Адрес:</dt>
                                        <dd class="col-sm-8">{{ $user->address ?? 'Не указан' }}</dd>

                                        <dt class="col-sm-4">Дата регистрации:</dt>
                                        <dd class="col-sm-8">{{ $user->created_at->format('d.m.Y') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Статистика</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-6">Всего записей:</dt>
                                        <dd class="col-sm-6">{{ $user->appointments->count() }}</dd>

                                        <dt class="col-sm-6">Завершенных:</dt>
                                        <dd class="col-sm-6">{{ $user->appointments->where('status', 'completed')->count() }}</dd>

                                        <dt class="col-sm-6">Ожидающих:</dt>
                                        <dd class="col-sm-6">{{ $user->appointments->where('status', 'pending')->count() }}</dd>

                                        <dt class="col-sm-6">Подтвержденных:</dt>
                                        <dd class="col-sm-6">{{ $user->appointments->where('status', 'confirmed')->count() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">История записей</h5>
                                </div>
                                <div class="card-body p-0">
                                    @if($user->appointments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Врач</th>
                                                        <th>Дата и время</th>
                                                        <th>Статус</th>
                                                        <th>Услуги</th>
                                                        <th>Стоимость</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->appointments->sortByDesc('created_at') as $appointment)
                                                    <tr>
                                                        <td>{{ $appointment->doctor ? $appointment->doctor->user->name : 'Неизвестный врач' }}</td>
                                                        <td>{{ $appointment->schedule && $appointment->schedule->date ? \Carbon\Carbon::parse($appointment->schedule->date)->format('d.m.Y') . ' ' . \Carbon\Carbon::parse($appointment->schedule->time_slot)->format('H:i') : 'Расписание не указано' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{
                                                                $appointment->status == 'confirmed' ? 'success' :
                                                                ($appointment->status == 'completed' ? 'primary' :
                                                                ($appointment->status == 'pending' ? 'warning' :
                                                                ($appointment->status == 'cancelled' ? 'danger' : 'secondary')))
                                                            }}">
                                                                {{
                                                                    $appointment->status == 'confirmed' ? 'Подтверждено' :
                                                                    ($appointment->status == 'completed' ? 'Завершено' :
                                                                    ($appointment->status == 'pending' ? 'Ожидает' :
                                                                    ($appointment->status == 'cancelled' ? 'Отменено' : $appointment->status)))
                                                                }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            {{ $appointment->services && $appointment->services->count() ? $appointment->services->pluck('name')->join(', ') : 'Услуги не указаны' }}
                                                        </td>
                                                        <td>
                                                            {{ $appointment->services && $appointment->services->count() ? number_format($appointment->services->sum('price')) . ' ₽' : '-' }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-3">
                                            <p class="text-muted mb-0">Записи для этого пациента не найдены.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-primary">&larr; Назад к пациентам</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary ms-2">Назад к панели</a>
    </div>
</div>
@endsection
