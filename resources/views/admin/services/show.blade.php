@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Service Details</h1>
            <a href="{{ route('admin.services.edit', $service) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition duration-200">
                Edit Service
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $service->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <p class="mt-1 text-lg font-semibold text-green-600">${{ number_format($service->price, 2) }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Description</h2>
                <div>
                    <p class="text-gray-700">{{ $service->description ?? 'No description provided.' }}</p>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Assigned Doctors</h2>
            @if($service->doctors->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($service->doctors as $doctor)
                    <div class="border rounded-lg p-4">
                        <h3 class="font-medium">{{ $doctor->user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $doctor->specialization->name }}</p>
                        <p class="text-sm text-gray-500">{{ $doctor->experience_years }} years experience</p>
                        <div class="mt-2">
                            <a href="{{ route('admin.doctors.show', $doctor) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Doctor</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No doctors are assigned to this service.</p>
            @endif
        </div>

        <div>
            <h2 class="text-xl font-semibold mb-4">Recent Appointments</h2>
            @if($service->appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($service->appointments->take(10) as $appointment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->patient ? $appointment->patient->name : 'Unknown Patient' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->doctor && $appointment->doctor->user ? $appointment->doctor->user->name : 'Unknown Doctor' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->schedule && $appointment->schedule->start_time ? $appointment->schedule->start_time->format('M d, Y H:i') : 'No Schedule' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($service->appointments->count() > 10)
                    <p class="text-sm text-gray-600 mt-2">Showing 10 most recent appointments.</p>
                @endif
            @else
                <p class="text-gray-500">No appointments found for this service.</p>
            @endif
        </div>

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('admin.services.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Services</a>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
