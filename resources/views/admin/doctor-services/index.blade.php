@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Управление связями врач-услуга</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">&larr; Назад к панели</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-muted mb-4">Настройте, какие услуги может предоставлять каждый врач. Это влияет на доступность записи на прием.</p>

        <form method="POST" action="{{ route('admin.doctor-services.update') }}">
            @csrf

            <div class="space-y-8">
                @foreach($doctors as $doctor)
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-lg">{{ substr($doctor->user->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $doctor->user->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $doctor->specialization->name }} • Стаж {{ $doctor->experience_years }} лет</p>
                        </div>
                    </div>

                    <div class="ml-16">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Доступные услуги</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($services as $service)
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="doctor_services[{{ $doctor->id }}][]"
                                           value="{{ $service->id }}"
                                           id="doctor_{{ $doctor->id }}_service_{{ $service->id }}"
                                           {{ $doctor->services->contains($service->id) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="doctor_{{ $doctor->id }}_service_{{ $service->id }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $service->name }}
                                        <span class="text-gray-500">(${{
                                            number_format($service->price, 2) }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    Отмена
                </a>
                <button type="submit" class="btn btn-success">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
