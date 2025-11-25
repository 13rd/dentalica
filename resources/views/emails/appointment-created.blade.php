@component('mail::message')
# Appointment Confirmed

Date: {{ $appointment->schedule->date }}
Time: {{ $appointment->schedule->time_slot }}
Doctor: {{ $appointment->doctor->user->name }}

Thanks,
{{ config('app.name') }}
@endcomponent
