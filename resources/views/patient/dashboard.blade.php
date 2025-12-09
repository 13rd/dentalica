@extends('layouts.app')

@section('content')
    <h1>My Appointments</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Services</th>
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
                        @forelse($app->services as $service)
                            <span class="badge bg-info text-dark me-1">{{ $service->name }}</span>
                        @empty
                            <span class="text-muted">Консультация</span>
                        @endforelse
                    </td>
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
                        @if($app->status !== 'cancelled' && $app->status !== 'completed')
                            <form method="POST" action="{{ route('appointment.cancel', $app) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('{{ $app->payment_status === 'paid' ? 'Отменить запись? Деньги будут возвращены в течение 2 часов.' : 'Отменить запись?' }}')">
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
                                <div class="modal-body py-4">
                                    <div class="mb-3 text-center">
                                        <h4 class="mb-1">К оплате: <strong>{{ number_format($app->total_price) }} ₽</strong></h4>
                                        <p class="text-muted mb-0">Врач: {{ $app->doctor->user->name }}</p>
                                        <p class="text-muted">{{ \Carbon\Carbon::parse($app->schedule->date)->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($app->schedule->time_slot)->format('H:i') }}</p>
                                    </div>

                                    <div class="border rounded p-3 mb-3 bg-light">
                                        <h6 class="mb-3 text-success">Что входит:</h6>
                                        @if($app->services->count())
                                            <ul class="list-group list-group-flush">
                                                @foreach($app->services as $service)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $service->name }}</span>
                                                        <span class="fw-semibold">{{ number_format($service->price) }} ₽</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-muted">Консультация (включено)</div>
                                        @endif
                                    </div>

                                    <div class="text-center">
                                        <button type="button" class="btn btn-outline-success px-4" onclick="toggleCard({{ $app->id }})">
                                            Перейти к оплате картой
                                        </button>
                                    </div>

                                    <div id="cardSection{{ $app->id }}" class="d-none mt-4">
                                        <p class="text-muted text-center mb-3">Введите данные карты</p>
                                        <input type="text" class="form-control form-control-lg text-center mb-3" placeholder="Номер карты: 4242 4242 4242 4242" value="4242 4242 4242 4242" readonly>
                                        <input type="text" class="form-control form-control-lg text-center" placeholder="CVV: 123" value="123" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5" id="payBtn{{ $app->id }}" disabled>
                                        Оплатить {{ number_format($app->total_price) }} ₽
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
    <script>
        function toggleCard(id) {
            const section = document.getElementById('cardSection' + id);
            const btn = document.getElementById('payBtn' + id);
            if (section && btn) {
                section.classList.remove('d-none');
                btn.disabled = false;
            }
        }
    </script>
@endsection
