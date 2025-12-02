@extends('layouts.app')

@section('content')
    <h1>My Appointments</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $app)
<tr>
    <td>{{ \Carbon\Carbon::parse($app->schedule->date)->format('d.m.Y') }}</td>
    <td>{{ \Carbon\Carbon::parse($app->schedule->time_slot)->format('H:i') }}</td>
    <td>{{ $app->doctor->user->name }}</td>
    <td>
        <span class="badge bg-{{ $app->status == 'confirmed' ? 'success' : 'secondary' }}">
            {{ $app->status }}
        </span>
    </td>
    <td>
        <span class="badge bg-{{ $app->payment_status == 'paid' ? 'success' : 'warning' }}">
            {{ $app->payment_status == 'paid' ? 'Оплачено' : 'Ожидает оплаты' }}
        </span>
        @if($app->payment_status == 'pending' && $app->expires_at && now()->lessThan($app->expires_at))
            <small class="text-danger d-block">
                Осталось: {{ round(now()->diffInMinutes($app->expires_at)) }} мин
            </small>
        @endif
    </td>
    <td>{{ number_format($app->total_price) }} ₽</td>
    <td>
        @if($app->status !== 'cancelled' && $app->payment_status !== 'paid')
    <form method="POST" action="{{ route('appointment.cancel', $app) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm"
                onclick="return confirm('Отменить запись?')">
            Отменить
        </button>
    </form>
@endif
        @if($app->payment_status == 'pending' && $app->expires_at && now()->lessThan($app->expires_at))
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal{{ $app->id }}">
                Оплатить
            </button>
        @elseif($app->payment_status == 'paid')
            <span class="text-success">Оплачено</span>
        @else
            <span class="text-danger">Отменено</span>
        @endif
    </td>
</tr>

<!-- Модальное окно оплаты -->
<div class="modal fade" id="payModal{{ $app->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Оплата приёма</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('appointment.process-payment', $app) }}">
                @csrf
                <div class="modal-body text-center py-5">
                    <h4>К оплате: <strong>{{ number_format($app->total_price) }} ₽</strong></h4>
                    <p class="text-muted">Врач: {{ $app->doctor->user->name }}</p>
                    <p class="text-muted">{{ \Carbon\Carbon::parse($app->schedule->date)->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($app->schedule->time_slot)->format('H:i') }}</p>

                    <div class="my-4">
                        <input type="text" class="form-control form-control-lg text-center" placeholder="Номер карты: 4242 4242 4242 4242" value="4242 4242 4242 4242" readonly>
                        <input type="text" class="form-control form-control-lg text-center mt-3" placeholder="CVV: 123" value="123" readonly>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        Оплатить {{ number_format($app->total_price) }} ₽
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach        </tbody>
    </table>
@endsection
