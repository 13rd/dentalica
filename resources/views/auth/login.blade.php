@extends('layouts.guest')

@section('title', 'Вход')

@section('content')
<div class="auth-shell">
    <div class="app-card auth-card p-4 p-sm-5 shadow-soft">
        <div class="text-center mb-3">
            <h2 class="page-title mb-1">Вход в личный кабинет</h2>
            <p class="auth-subtitle">Запишитесь к стоматологу онлайн</p>
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

        <form method="POST" action="{{ route('login') }}" class="mt-4 d-flex align-items-center justify-content-center flex-column gap-4">
            @csrf

            <div class="col-4">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required
                       class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       placeholder="email@example.com" value="{{ old('email') }}" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-4">
                <label for="password" class="form-label">Пароль</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="Ваш пароль">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input id="remember" name="remember" type="checkbox" class="form-check-input">
                    <label for="remember" class="form-check-label">Запомнить меня</label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link-clean text-decoration-none text-primary">
                        Забыли пароль?
                    </a>
                @endif
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-brand">Войти</button>
            </div>

            <div class="text-center mt-3">
                <p class="auth-subtitle mb-0">
                    Нет аккаунта?
                    <a href="{{ route('register') }}" class="link-clean text-primary fw-semibold">Зарегистрироваться</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
