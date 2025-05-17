@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Generate Schedule" data-parent="Schedules">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">

                {{-- Back Button --}}
                <div class="flex justify-start mt-6 mb-4">
                    <a href="{{ route('schedules.index') }}"
                        onclick="event.preventDefault(); 
                                                                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                                       .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Schedules'); 
                                                                   const title = schoolYearLink?.getAttribute('data-title') || 'Schedules'; 
                                                                    loadContent('{{ route('schedules.index') }}', title, 'schedules');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Schedule List
                    </a>
                </div>

                {{-- Conflict Error --}}
                @if (session('info'))
                    <div id="infoAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong class="font-bold">Info:</strong>
                        <span class="block sm:inline">{{ session('info') }}</span>
                    </div>
                @endif

                @if ($errors->has('conflict'))
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong class="font-bold">Schedule Conflicts Found:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->get('conflict') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Card --}}
                <div class="bg-yellow-100 shadow-lg rounded-lg p-8">
                    <form id="generateScheduleForm" action="{{ route('schedules.auto-generate') }}" method="POST"
                        class="space-y-6">
                        @csrf

                        {{-- School Year --}}
                        <div class="mb-4 text-left">
                            <label for="school_year_id" class="block text-gray-800 font-medium mb-2 text-xl">
                                School Year
                            </label>
                            <select name="school_year_id" id="school_year_id"
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                                required>
                                @foreach ($schoolYears as $schoolYear)
                                    <option value="{{ $schoolYear->id }}">{{ $schoolYear->start_year }} -
                                        {{ $schoolYear->end_year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Teacher --}}
                        <div class="mb-4 text-left">
                            <label for="teacher_id" class="block text-gray-800 font-medium mb-2 text-xl">
                                Teacher
                            </label>
                            <select name="teacher_id" id="teacher_id"
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                                required>
                                <option value="">Select Teacher</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Course --}}
                        <div class="mb-4 text-left">
                            <label for="course_id" class="block text-gray-800 font-medium mb-2 text-xl">
                                Subject
                            </label>
                            <select name="course_id" id="course_id"
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                                required>
                                <option value="">Select Subject</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('subject_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} ({{ $course->grade_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Time Slot --}}
                        <div class="mb-4 text-left">
                            <label for="time_slot" class="block text-gray-800 font-medium mb-2 text-xl">
                                Time Slot
                            </label>
                            <select name="time_slot" id="time_slot"
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400"
                                required>
                                <option value="" disabled {{ old('time_slot') ? '' : 'selected' }}>Select Time Slot</option>
                                <option value="morning" {{ old('time_slot') == 'morning' ? 'selected' : '' }}>Morning (7:00 AM - 12:00 PM)</option>
                                <option value="afternoon" {{ old('time_slot') == 'afternoon' ? 'selected' : '' }}>Afternoon (1:00 PM - 6:00 PM)</option>
                            </select>
                        </div>

                        {{-- Submit --}}
                        <div class="flex justify-end pt-4">
                            <button type="submit" id="generateScheduleBtn"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Generate Schedule
                            </button>
                        </div>
                    </div>
                </form>


                {{-- Spinner and Modal Styles --}}
                <style>
                    .dots::after {
                        content: '.';
                        animation: dots 1.2s steps(3, end) infinite;
                    }

                    @keyframes dots {
                        0% {
                            content: '.';
                        }

                        33% {
                            content: '..';
                        }

                        66% {
                            content: '...';
                        }

                        100% {
                            content: '.';
                        }
                    }

                    select.custom-select {
                        appearance: none;
                        -webkit-appearance: none;
                        -moz-appearance: none;
                        background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                        background-repeat: no-repeat;
                        background-position: right 1rem center;
                        background-size: 1.25rem;
                        padding-right: 3rem;
                    }

                    .fade-in {
                        opacity: 1;
                        transform: translateY(0);
                        max-height: 500px;
                        /* enough space for content */
                        margin-bottom: 1rem;
                        padding-top: 1rem;
                        padding-bottom: 1rem;
                        transition: all 0.5s ease-in-out;
                        overflow: hidden;
                    }

                    .fade-out {
                        opacity: 0;
                        transform: translateY(20px);
                        max-height: 0;
                        margin-bottom: 0;
                        padding-top: 0;
                        padding-bottom: 0;
                        transition: all 0.5s ease-in-out;
                        overflow: hidden;
                    }
                </style>
            </div>
        </div>
          {{-- JS Handling --}}
        <script>
        document.getElementById('generateScheduleForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('generateScheduleBtn');
            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="dots">Checking</span>`;

        document.addEventListener('DOMContentLoaded', () => {
            const fadeWithDelay = (id) => {
                const el = document.getElementById(id);
                if (el) {
                    // Add fade-in immediately
                    el.classList.add('fade-in');

                    // Then remove fade-in and add fade-out after 5s
                    setTimeout(() => {
                        el.classList.remove('fade-in');
                        el.classList.add('fade-out');
                    }, 5000);
                }
            };

            fadeWithDelay('infoAlert');
            fadeWithDelay('errorAlert');
        });

            // If you intend to submit the form via AJAX, you would do it here.
            // For a standard form submission, this will allow the browser to proceed.
            form.submit(); // Or use AJAX
        });
    </script>
    </div>
@endsection