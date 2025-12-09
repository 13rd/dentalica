<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Dental Appointment') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">Dental Appointment</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @if (auth()->check())
                    <!-- Приветствие пользователя -->
                    <li class="nav-item me-3 d-flex align-items-center">
                        <span class="badge text-bg-primary">
                            Пользователь: {{ auth()->user()->name }}
                            @if (auth()->user()->role)
                                <span class="text-white-50 ms-2">({{ ucfirst(auth()->user()->role) }})</span>
                            @endif
                        </span>
                    </li>

                    @if (auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                    @elseif (auth()->user()->isDoctor())
                        <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}">Doctor Dashboard</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('patient.dashboard') }}">Patient Dashboard</a></li>
                    @endif

                    @if (auth()->user()->isPatient())
                        <li class="nav-item"><a class="nav-link" href="{{ route('patient.appointments.week') }}">Записаться</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('doctors.index') }}">Doctors</a></li>
                    @endif

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link text-danger">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>    <main class="container py-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
