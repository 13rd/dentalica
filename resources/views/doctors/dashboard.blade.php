@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Добрый день, {{ auth()->user()->name }}!</h1>
    <p>Ваша специализация: <strong>{{ $doctor->specialization->name }}</strong></p>

    <div class="row mt-5">
        <div class="col-md-6">
            <h3>Сегодняшние пациенты</h3>
            @if($todayAppointments->count())
                <table class="table table-bordered">
                    <thead><tr><th>Время</th><th>Пациент</th><th>Услуги</th><th>Действие</th></tr></thead>
                    <tbody>
                    @foreach($todayAppointments as $app)
                        <tr class="{{ $app->status == 'completed' ? 'table-success' : '' }}">
                            <td>{{ $app->schedule->time_slot->format('H:i') }}</td>
                            <td>{{ $app->patient->name }}<br><small>{{ $app->patient->phone }}</small></td>
                            <td>
                                @foreach($app->services as $s)
                                    <span class="badge bg-info">{{ $s->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($app->status !== 'completed')
                                    <form method="POST" action="{{ route('doctor.appointment.complete', $app) }}" style="display:inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Завершить</button>
                                    </form>
                                @else
                                    Завершён
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Сегодня пациентов нет</p>
            @endif
        </div>

        <div class="col-md-6">
            <h3>Ближайшие приёмы</h3>
            @if($upcomingAppointments->count())
                <ul class="list-group">
                    @foreach($upcomingAppointments as $app)
                        <li class="list-group-item">
                            <strong>{{ $app->schedule->date->format('d.m.Y') }} в {{ $app->schedule->time_slot->format('H:i') }}</strong><br>
                            {{ $app->patient->name }} ({{ $app->patient->phone }})
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Записей нет</p>
            @endif
        </div>
    </div>
</div>
@endsection
