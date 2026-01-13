@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Регистрация нового врача</h4>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-primary btn-sm">&larr; Назад к врачам</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.doctors.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Полное имя</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="form-control">
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Адрес электронной почты</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="form-control">
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Пароль</label>
                                <input type="password" name="password" id="password" required
                                       class="form-control">
                                @error('password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="specialization_id" class="form-label">Специализация</label>
                                <select name="specialization_id" id="specialization_id" required class="form-select">
                                    <option value="">Выберите специализацию</option>
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization->id }}" {{ old('specialization_id') == $specialization->id ? 'selected' : '' }}>
                                            {{ $specialization->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('specialization_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="experience_years" class="form-label">Стаж (лет)</label>
                                <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years') }}" min="0" required
                                       class="form-control">
                                @error('experience_years')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="bio" class="form-label">Биография</label>
                            <textarea name="bio" id="bio" rows="4" class="form-control">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Услуги (Необязательно)</label>
                            <div class="form-text mb-3">Вы также можете назначить услуги этому врачу после регистрации или сделать это сейчас.</div>
                            <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                <div class="row">
                                    @php
                                        $services = \App\Models\Service::all();
                                    @endphp
                                    @foreach($services as $service)
                                        <div class="col-12 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" name="services[]" value="{{ $service->id }}" id="service_{{ $service->id }}"
                                                       class="form-check-input">
                                                <label for="service_{{ $service->id }}" class="form-check-label">
                                                    <strong>{{ $service->name }}</strong><br>
                                                    <small class="text-muted">${{ number_format($service->price, 2) }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('services')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Выберите услуги, которые может предоставлять этот врач.</div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">
                                Отмена
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Зарегистрировать врача
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
