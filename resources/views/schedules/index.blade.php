@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
                <h2 class="text-2xl font-bold">Schedule Management</h2>
            </div>
            <div class="space-x-4">
                <a href="{{ route('schedules.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add Schedule
                </a>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schedules as $schedule)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->teacher->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->course->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->section->name }} (Grade {{ $schedule->section->grade_level }})</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($schedule->day_of_week)
                                @case('Monday')
                                    Monday
                                    @break
                                @case('Tuesday')
                                    Tuesday
                                    @break
                                @case('Wednesday')
                                    Wednesday
                                    @break
                                @case('Thursday')
                                    Thursday
                                    @break
                                @case('Friday')
                                    Friday
                                    @break
                                @default
                                    {{ $schedule->day_of_week }}
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('schedules.edit', $schedule) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection