@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Edit Subject" data-parent="Subjects">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">

                {{-- Back Button Left-Aligned --}}
                <div class="flex justify-start mb-4">
                    <a href="{{ route('courses.index') }}" onclick="event.preventDefault(); 
                                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                       .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Subjects'); 
                                   const title = schoolYearLink?.getAttribute('data-title') || 'Subjects'; 
                                    loadContent('{{ route('courses.index') }}', title, 'courses');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to List
                    </a>
                </div>

                {{-- Error Box --}}
                @if ($errors->any())
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Card --}}
                <div class="bg-yellow-100 shadow-lg rounded-lg p-8 transition">
                    <form action="{{ route('courses.update', $course->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6 text-left">
                            <label class="block text-gray-800 font-medium mb-2" for="code" style="font-size: 22px;">Subject
                                Code</label>
                            <input
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                id="code" type="text" name="code" value="{{ old('code', $course->code) }}" required>
                        </div>

                        <div class="mb-6 text-left">
                            <label class="block text-gray-800 font-medium mb-2" for="name" style="font-size: 22px;">Subject
                                Name</label>
                            <input
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                id="name" type="text" name="name" value="{{ old('name', $course->name) }}" required>
                        </div>

                        <div class="mb-6 text-left">
                            <label class="block text-gray-800 font-medium mb-2" for="grade_level"
                                style="font-size: 22px;">Grade Level</label>
                            <select id="grade_level" name="grade_level" required
                                class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 text-gray-800">
                                <option value="" disabled selected>Select Grade Level</option>
                                @foreach($gradeLevels as $gradeLevel)
                                    <option value="{{ $gradeLevel->grade_level }}" {{ old('grade_level', $course->grade_level) == $gradeLevel->grade_level ? 'selected' : '' }}>
                                        {{ $gradeLevel->grade_level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6 text-left">
                            <label class="block text-gray-800 font-medium mb-2" for="description"
                                style="font-size: 22px;">Description</label>
                            <textarea
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                id="description" name="description"
                                rows="4">{{ old('description', $course->description) }}</textarea>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                                Update
                            </button>
                        </div>

                        <style>
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

                                fadeWithDelay('errorAlert');
                            });
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection