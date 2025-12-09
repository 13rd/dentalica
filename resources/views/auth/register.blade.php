@extends('layouts.guest')

@section('title', 'Регистрация')

@section('content')
<div class="auth-shell">
    <div class="app-card auth-card p-4 p-sm-5 shadow-soft">
        <div class="text-center mb-3">
            <h2 class="page-title mb-1">Создать аккаунт пациента</h2>
            <p class="auth-subtitle">Доступ к онлайн-записи и профилю</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="mt-4">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                       class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class "form-label">Email</label>
                <input id="email" name="email" type="email" required value="{{ old('email') }}"
                       class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Телефон (необязательно)</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                       class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input id="password" name="password" type="password" required
                       class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-brand">Зарегистрироваться</button>
            </div>

            <div class="text-center mt-3">
                <p class="auth-subtitle mb-0">
                    Уже есть аккаунт?
                    <a href="{{ route('login') }}" class="link-clean text-primary fw-semibold">
                        Войти
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
