@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Sections Management</h1>

            <div class="flex flex-wrap gap-2 justify-end">
                <a href="{{ route('sections.archivedIndex') }}"
                    onclick="event.preventDefault(); loadContent('{{ route('sections.archivedIndex') }}', 'View Archived Section', 'sections');"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                    View Archived Sections
                </a>
                <a href="{{ route('sections.create') }}"
                    onclick="event.preventDefault(); loadContent('{{ route('sections.create') }}', 'Add Section', 'sections');"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Add New Section
                </a>
            </div>
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


        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full text-center text-sm divide-y divide-gray-300">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Section Name</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Adviser</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">School Year</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($sections->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No sections available for the active school year.
                            </td>
                        </tr>
                    @else
                        @foreach($sections as $section)
                            <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $section->name }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $section->adviser->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $section->grade_level }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $section->schoolYear->start_year }} -
                                    {{ $section->schoolYear->end_year }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('sections.assignStudentsForm', $section) }}"
                                     onclick="event.preventDefault(); loadContent('{{ route('sections.assignStudentsForm', $section) }}', 'Assign Students', 'sections');"
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                        Assign Students
                                    </a>
                                    <a href="{{ route('sections.edit', $section) }}"
                                        onclick="event.preventDefault(); loadContent('{{ route('sections.edit', $section) }}', 'Edit Section', 'sections');"
                                        class="bg-green-500 hover:bg-green-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('sections.archive', $section) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                            Archive
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="px-6">
                {{ $sections->links() }}
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