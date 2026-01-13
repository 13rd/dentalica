@component('mail::message')
# Запись подтверждена

Дата: {{ $appointment->schedule->date }}
Время: {{ $appointment->schedule->time_slot }}
Врач: {{ $appointment->doctor->user->name }}

Спасибо,
{{ config('app.name') }}
@endcomponent
