@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-4">
        <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">
            <span class="inline-flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </span>
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold">My Teaching Schedule</h2>
            
            <!-- Day tabs -->
            <div class="flex space-x-1 mt-4 border-b">
                @foreach($days as $day)
                    <a href="{{ route('teacher.schedule', ['day' => $day]) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-t-lg {{ $selectedDay === $day 
                           ? 'bg-indigo-600 text-white' 
                           : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                        {{ $day }}
                    </a>
                @endforeach
            </div>
        </div>
        
        <div class="overflow-x-auto p-4">
            <table class="min-w-full border border-gray-200">
                <thead>
                    <tr>
                        <th class="border border-gray-200 px-4 py-2 bg-gray-50">Time</th>
                        @foreach($sections as $section)
                            <th class="border border-gray-200 px-4 py-2 bg-gray-50 text-sm">
                                {{ $section->grade_level }}-{{ $section->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $timeSlot)
                        <tr>
                            <td class="border border-gray-200 px-4 py-2 bg-gray-50 font-medium text-sm">{{ $timeSlot }}</td>
                            @foreach($sections as $section)
                                @php
                                    $sectionSchedules = $scheduledSections->get($section->id, collect());
                                @endphp
                                <td class="border border-gray-200 px-4 py-2 text-center {{ 
                                    $sectionSchedules->first(function($schedule) use ($timeSlot) {
                                        $slotStart = substr($timeSlot, 0, 5);
                                        $slotEnd = substr($timeSlot, -5);
                                        return $schedule->start_time === $slotStart && $schedule->end_time === $slotEnd;
                                    }) ? 'bg-indigo-100' : '' 
                                }}">
                                    @php
                                        $currentSchedule = $sectionSchedules->first(function($schedule) use ($timeSlot) {
                                            $slotStart = substr($timeSlot, 0, 5);
                                            $slotEnd = substr($timeSlot, -5);
                                            return $schedule->start_time === $slotStart && $schedule->end_time === $slotEnd;
                                        });
                                    @endphp
                                    
                                    @if($currentSchedule)
                                        <div class="text-sm">
                                            <div class="font-medium text-indigo-600">{{ $currentSchedule->course->name }}</div>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection