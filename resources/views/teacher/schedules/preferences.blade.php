@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Schedule Preferences</h1>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-4">Current Schedule</h2>
            @if($schedules->isEmpty())
                <p class="text-gray-600">No schedules assigned yet.</p>
            @else
                <div class="grid gap-4">
                    @foreach($schedules as $schedule)
                    <div class="border p-4 rounded-lg">
                        <p class="font-medium">{{ $schedule->course->name }}</p>
                        <p class="text-gray-600">
                            {{ $schedule->day_of_week }} ({{ date('h:i A', strtotime($schedule->start_time)) }} - {{ date('h:i A', strtotime($schedule->end_time)) }})
                        </p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection