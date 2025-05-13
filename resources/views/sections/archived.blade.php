@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="View Archived Section" data-parent="Section">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold self-center">Archived Sections</h1>

                <div class="flex items-center">
                    <a href="{{ route('sections.index') }}" onclick="event.preventDefault(); 
                                const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                    .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Sections'); 
                                const title = schoolYearLink?.getAttribute('data-title') || 'Sections'; 
                                loadContent('{{ route('sections.index') }}', title, 'sections');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Section List
                    </a>
                </div>
            </div>


            <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
                <table class="min-w-full text-center text-sm divide-y divide-gray-300">
                    <thead class="bg-yellow-100">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Section Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Grade Level</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">School Year</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Date Archived
                            </th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sections as $section)
                            <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $section->name }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $section->grade_level }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $section->schoolYear->start_year ?? 'N/A' }} -
                                    {{ $section->schoolYear->end_year ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $section->deleted_at ? $section->deleted_at->format('Y-m-d h:i A') : 'N/A' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('sections.restore', $section->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                            Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No archived sections found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6">
                    {{ $sections->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection