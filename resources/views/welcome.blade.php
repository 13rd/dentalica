{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row text-center">
        <div class="col-lg-8 mx-auto">
            <h1 class="display-4 mb-4">Стоматология нового уровня</h1>
            <p class="lead mb-5">Запишитесь к лучшим врачам онлайн за 30 секунд</p>

            <div class="d-grid gap-3 d-md-flex justify-content-center">
                @auth
                    <a href="{{ route('doctors.index') }}" class="btn btn-primary btn-lg px-5">Найти врача</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">Регистрация</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-5">Войти</a>
                @endauth
            </div>

            <div class="mt-5">
                <a href="{{ route('doctors.index') }}" class="text-decoration-none">
                    <h5>Посмотреть всех врачей →</h5>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
