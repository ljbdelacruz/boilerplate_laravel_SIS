@extends('dashboard.admin')

@section('title', 'Edit Schedule')

@section('content')
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">

            {{-- Back Button --}}
            <div class="flex justify-start mb-4">
                <a href="{{ route('schedules.index') }}"
                   onclick="event.preventDefault(); 
                            const scheduleLink = [...document.querySelectorAll('.nav-link')]
                                .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Schedules'); 
                            loadContent('{{ route('schedules.index') }}', scheduleLink || 'Schedules');"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    ← Back to Schedule List
                </a>
            </div>

            {{-- Error Display --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Edit Form --}}
            <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @php
                        $fields = [
                            'teacher_id' => 'Teacher',
                            'course_id' => 'Course',
                            'section_id' => 'Section',
                            'school_year_id' => 'School Year',
                            'day_of_week' => 'Day',
                            'start_time' => 'Start Time',
                            'end_time' => 'End Time',
                        ];
                    @endphp

                    <div class="mb-6 text-left">
                        <label for="teacher_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Teacher</label>
                        <select name="teacher_id" id="teacher_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $schedule->teacher_id == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6 text-left">
                        <label for="course_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Course</label>
                        <select name="course_id" id="course_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $schedule->course_id == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6 text-left">
                        <label for="section_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Section</label>
                        <select name="section_id" id="section_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ $schedule->section_id == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }} (Grade {{ $section->grade_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6 text-left">
                        <label for="school_year_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">School Year</label>
                        <select name="school_year_id" id="school_year_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select School Year</option>
                            @foreach($schoolYears as $sy)
                                <option value="{{ $sy->id }}" {{ $schedule->school_year_id == $sy->id ? 'selected' : '' }}>
                                    {{ $sy->start_year }} - {{ $sy->end_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6 text-left">
                        <label for="day_of_week" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Day</label>
                        <select name="day_of_week" id="day_of_week" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select Day</option>
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                <option value="{{ $day }}" {{ $schedule->day_of_week == $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6 text-left">
                        <label for="start_time" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">Start Time</label>
                        <input type="time" id="start_time" name="start_time" required
                               value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div class="mb-6 text-left">
                        <label for="end_time" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">End Time</label>
                        <input type="time" id="end_time" name="end_time" required
                               value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div class="flex justify-end pt-4">
                        <a href="#"
                           onclick="event.preventDefault();
                                    const form = this.closest('form');
                                    const formData = new FormData(form);
                                    const scheduleLink = [...document.querySelectorAll('.nav-link')]
                                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Schedules');

                                    const loadingPopup = document.createElement('div');
                                    loadingPopup.className = 'fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50';
                                    loadingPopup.innerHTML = 
                                        `<div class='bg-white p-6 rounded shadow text-center'>
                                            <div class='custom-spinner h-10 w-10 mx-auto mb-2'></div>
                                            <p class='text-gray-700 font-medium'>Updating Schedule...</p>
                                        </div>`;
                                    document.body.appendChild(loadingPopup);

                                    fetch(form.action, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': form.querySelector('[name=_token]').value,
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        body: formData
                                    }).then(response => {
                                        if (response.ok) {
                                            setTimeout(() => {
                                                loadingPopup.remove();
                                                const successPopup = document.createElement('div');
                                                successPopup.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-500 text-white px-6 py-4 rounded shadow-lg text-lg font-semibold z-50';
                                                successPopup.textContent = '✅ Schedule updated successfully!';
                                                document.body.appendChild(successPopup);

                                                setTimeout(() => {
                                                    successPopup.remove();
                                                    loadContent('{{ route('schedules.index') }}', scheduleLink || 'Schedules');
                                                }, 700);
                                            }, 500);
                                        } else {
                                            loadingPopup.remove();
                                            alert('Something went wrong. Please check your input.');
                                        }
                                    }).catch(error => {
                                        loadingPopup.remove();
                                        alert('An error occurred while updating.');
                                    });"
                           class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Update Schedule
                        </a>
                    </div>

                    <style>
                        .custom-spinner {
                            border: 4px solid #3b82f6;
                            border-top-color: transparent;
                            border-radius: 50%;
                            animation: spin-slow 2s linear infinite;
                        }

                        @keyframes spin-slow {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                </form>
            </div>
        </div>
    </div>
@endsection
