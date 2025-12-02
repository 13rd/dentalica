@extends('layouts.app')

@section('title', 'Моё расписание')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-primary">Моё расписание</h1>

    @forelse($schedules as $date => $slots)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y (l)') }}
                    @if($date == today()->format('Y-m-d'))
                        <span class="badge bg-light text-dark ms-2">Сегодня</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @foreach($slots as $slot)
                    @php
                        $appointment = $slot->appointment;
                        $isAvailable = $slot->is_available || ($appointment && $appointment->status === 'cancelled');
                    @endphp

                    <div class="border rounded p-3 mb-3 {{ $isAvailable ? 'bg-light' : 'bg-white' }} shadow-sm">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <strong class="text-primary">
                                    {{ \Carbon\Carbon::parse($slot->time_slot)->format('H:i') }}
                                </strong>

                                @if($appointment && $appointment->status !== 'cancelled' && $appointment->payment_status === 'paid')
                                    <span class="badge bg-success ms-2">Оплачено</span>
                                @endif

                                @if(!$isAvailable && $appointment && $appointment->status !== 'cancelled')
                                    <div class="mt-2">
                                        <strong>Пациент:</strong>
                                        {{ $appointment->patient->user->name }}
                                        <small class="text-muted">(+{{ $appointment->patient->user->phone ?? 'телефон не указан' }})</small>
                                    </div>

                                    @if($appointment->services->count())
                                        <div class="mt-1">
                                            <strong>Услуги:</strong>
                                            <span class="text-success">
                                                {{ $appointment->services->pluck('name')->implode(', ') }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="mt-1">
                                        <strong>Итого:</strong>
                                        <span class="text-primary fw-bold">
                                            {{ number_format($appointment->total_price) }} ₽
                                        </span>
                                    </div>
                                @else
                                    <span class="text-success fw-bold">— Свободно —</span>
                                @endif
                            </div>

                            <div class="ms-3">
                                @if($appointment && $appointment->status !== 'cancelled')
                                    @if($appointment->payment_status === 'paid')
                                        <form method="POST" action="{{ route('doctor.complete', $appointment) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Завершить приём? Пациент будет отмечен как обслуженный.')">
                                                Завершить
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('doctor.cancel', $appointment) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm mt-1"
                                                onclick="return confirm('Отменить запись? Слот снова станет свободным.')">
                                            Отменить
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            Нет запланированных приёмов на ближайшие дни
        </div>
    @endforelse
</div>
@endsection
