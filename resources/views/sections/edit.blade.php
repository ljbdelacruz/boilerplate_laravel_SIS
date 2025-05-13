@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Edit Section" data-parent="Sections">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                {{-- Back Button --}}
                <div class="flex justify-start mb-6">
                    <a href="{{ route('sections.index') }}"onclick="event.preventDefault();
                                        const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                           .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Sections');
                                       const title = schoolYearLink?.getAttribute('data-title') || 'Sections';
                                        loadContent('{{ route('sections.index') }}', title, 'sections');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Section List
                    </a>
                </div>

                {{-- Error Display --}}
                @if ($errors->any())
                    <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Edit Form --}}
                <form action="{{ route('sections.update', $section->id) }}" method="POST"
                    class="bg-yellow-100 shadow-lg rounded-lg p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="mb-6 text-left">
                        <label for="name" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                            Section Name
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $section->name) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div class="mb-6 text-left">
                        <label for="grade_level" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                            Grade Level
                        </label>
                        <select name="grade_level" id="grade_level" required
                            class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select Grade Level</option>
                            @foreach ($gradeLevels as $gradeLevel)
                                <option value="{{ $gradeLevel->grade_level }}"
                                    {{ old('grade_level', $section->grade_level) == $gradeLevel->grade_level ? 'selected' : '' }}>
                                    {{ $gradeLevel->grade_level }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6 text-left">
                        <label for="adviser_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                            Adviser
                        </label>
                        <select name="adviser_id" id="adviser_id"
                            class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select an Adviser</option>
                            @foreach ($advisers as $adviser)
                                <option value="{{ $adviser->id }}"
                                    {{ (isset($section) && $section->adviser_id == $adviser->id) || old('adviser_id') == $adviser->id ? 'selected' : '' }}>
                                    {{ $adviser->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('adviser_id')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 text-left">
                        <label for="school_year_id" class="block text-gray-800 font-medium mb-2" style="font-size: 22px;">
                            School Year
                        </label>
                        <select name="school_year_id" id="school_year_id"
                            class="custom-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="">Select School Year</option>
                            @foreach ($schoolYears ?? [] as $schoolYear)
                                <option value="{{ $schoolYear->id }}"
                                    {{ old('school_year_id', $section->school_year_id) == $schoolYear->id ? 'selected' : '' }}>
                                    {{ $schoolYear->start_year }} - {{ $schoolYear->end_year }}
                                    @if ($schoolYear->is_active)
                                        (Active)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Submit Button with JS loading feedback --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Update Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    {{-- Spinner Style --}}
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
    </style>
    <script>
        documet.addEventListener('DOMContentLoaded', () => {
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
@endsection
