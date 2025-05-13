@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Assign Students" data-parent="Section">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    Assign Students to <span class="text-yellow-600">{{ $section->name }}
                        ({{ $section->grade_level }})</span>
                </h1>
                <a href="{{ route('sections.index') }}" onclick="event.preventDefault(); 
                                                const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                   .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Sections'); 
                                               const title = schoolYearLink?.getAttribute('data-title') || 'Sections'; 
                                                loadContent('{{ route('sections.index') }}', title, 'sections');"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                    ← Back to Section List
                </a>
            </div>

            @if (session('success'))
                <div id="successAlert"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div id="errorAlert"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm text-center">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Assignable Students -->
                <div class="bg-yellow-100 p-6 rounded-2xl shadow-lg border border-yellow-300">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Available Students</h2>

                    <form action="{{ route('sections.assignStudents', $section) }}" method="POST" class="space-y-4 text-sm">
                        @csrf

                        <div>
                            <label for="student_ids" class="block text-gray-800 font-medium mb-2 text-sm">
                                Select Students
                                <span class="text-xs text-gray-500">(Hold Ctrl/Cmd to select multiple)</span>
                            </label>
                            <select name="student_ids[]" id="student_ids" multiple
                                class="w-full border border-yellow-300 rounded-xl px-3 py-2 text-sm text-center bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-yellow-400 h-64 overflow-auto leading-tight shadow-inner">
                                @forelse($assignableStudents as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->last_name ?? '' }}, {{ $student->first_name ?? $student->name }}
                                        - LRN: {{ $student->lrn }}
                                        {{ $student->section ? ' - In: ' . $student->section->name : ' - Not assigned' }}
                                    </option>
                                @empty
                                    <option value="" disabled>No students available to assign.</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-5 rounded-lg shadow text-sm">
                                Assign Selected
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Currently Assigned Students -->
                <div class="bg-yellow-100 p-6 rounded-2xl shadow-lg border border-yellow-300">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">
                        Assigned to {{ $section->name }}
                        <span class="text-sm text-gray-500">({{ $currentStudents->count() }} students)</span>
                    </h2>

                    @if($currentStudents->count() > 0)
                        <div class="max-h-96 overflow-y-auto border rounded-lg bg-white">
                            <ul class="divide-y divide-gray-200">
                                @foreach($currentStudents as $student)
                                    <li
                                        class="px-4 py-3 bg-gray-50 hover:bg-yellow-50 transition flex flex-col sm:flex-row sm:justify-between">
                                        <span class="text-base text-gray-700 font-medium">{{ $student->last_name ?? '' }},
                                            {{ $student->first_name ?? $student->name }}</span>
                                        <span class="text-sm text-gray-500">LRN: {{ $student->lrn }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <p class="text-gray-500 italic text-lg">No students currently assigned.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <style>
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
        <script>
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

                fadeWithDelay('successAlert');
                fadeWithDelay('errorAlert');
            });
        </script>
    </div>
@endsection