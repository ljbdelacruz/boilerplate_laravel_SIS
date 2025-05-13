@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="View Archived Students" data-parent="Student">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold self-center">Archived Students</h1>

                <div class="flex items-center">
                    <a href="{{ route('admin.students.index') }}" onclick="event.preventDefault(); 
                                                    const studentLink = [...document.querySelectorAll('.nav-link')]
                                                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                                                    const title = studentLink?.getAttribute('data-title') || 'Students'; 
                                                    loadContent('{{ route('admin.students.index') }}', title, 'students');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Student List
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
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <!-- Left: Filter by School Year -->
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('admin.students.archivedIndex') }}" class="flex items-center gap-2">
                            <label for="school_year_id" class="text-sm font-medium text-gray-700">Filter by School Year:</label>
                            <select name="school_year_id" id="school_year_id" onchange="this.form.submit()"
                                class="custom-select border rounded p-2 text-sm">
                                <option value="">All School Years</option>
                                @foreach($schoolYears as $sy)
                                    <option value="{{ $sy->id }}" {{ $selectedSchoolYearId == $sy->id ? 'selected' : '' }}>
                                        {{ $sy->start_year }} - {{ $sy->end_year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <!-- Right: Search and Buttons -->
                    <div class="flex items-center gap-2">
                        <!-- Search Bar -->
                        <div class="relative w-72">
                            <img src="{{ asset('icons/searchlogo.png') }}" alt="Search Icon"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4">
                            <input type="text" id="searchArchiveStudent" placeholder="Search student by first or last name..."
                                class="bg-gray-100 text-sm text-gray-700 pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Filter Button -->
                        <button onclick="filterArchiveStudents()" id="filterButton"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                            Search
                        </button>

                        <!-- Clear Button -->
                        <button onclick="clearSearch()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded-lg text-sm">
                            Clear
                        </button>
                    </div>
                </div>

                <table id="archiveStudentTable" class="min-w-full text-center text-sm divide-y divide-gray-300">
                    <thead class="bg-yellow-100">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">LRN</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Grade &
                                Section
                            </th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Archived At
                            </th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($archivedStudents as $student)
                            <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $student->lrn }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $student->grade_level }} - {{ $student->section->name ?? 'N/A' }}
                                    <span class="block text-xs text-gray-500">S.Y.
                                        {{ $student->schoolYear->start_year ?? 'N/A' }}-{{ $student->schoolYear->end_year ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $student->deleted_at ? $student->deleted_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.students.archivedShow', $student->id) }}"
                                        onclick="event.preventDefault(); loadContent('{{ route('admin.students.archivedShow', $student->id) }}', 'View Archived Student','students');"
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                                        View
                                    </a>

                                    <!-- Restore Form Button -->
                                    <form action="{{ route('admin.students.unarchive', $student->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to restore this student?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                                            Restore
                                        </button>
                                    </form>
                                </div>
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No archived students found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6">
                    {{ $archivedStudents->links() }}
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

            select.custom-select {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.55rem center;
                background-size: 0.875rem;
                padding-right: 2.25rem;

                background-color: white;
                border: 1px solid #d1d5db;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
                transition: box-shadow 0.2s ease-in-out;
            }
        </style>
        <script>
            function filterArchiveStudents() {
                const input = document.getElementById("searchArchiveStudent").value.trim().toLowerCase();
                const rows = document.querySelectorAll("#archiveStudentTable tbody tr:not(.no-match-row)");
                let found = false;

                rows.forEach(row => {
                    const nameCell = row.children[0]; // Assuming the name is in the first column
                    const nameText = nameCell.textContent.trim().toLowerCase();
                    const match = nameText.includes(input);

                    row.style.display = match ? "table-row" : "table-row";
                    row.style.visibility = match ? "visible" : "collapse";
                    if (match) found = true;
                });

                const tbody = document.querySelector("#archiveStudentTable tbody");
                let noMatchRow = tbody.querySelector(".no-match-row");

                if (!found) {
                    if (!noMatchRow) {
                        noMatchRow = document.createElement("tr");
                        noMatchRow.classList.add("no-match-row");
                        noMatchRow.innerHTML = `
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 bg-white text-sm">
                            No archived student matches found
                        </td>`;
                        tbody.appendChild(noMatchRow);
                    }
                } else if (noMatchRow) {
                    noMatchRow.remove();
                }
            }

            function clearSearch() {
                document.getElementById("searchArchiveStudent").value = "";
                const rows = document.querySelectorAll("#archiveStudentTable tbody tr:not(.no-match-row)");
                rows.forEach(row => {
                    row.style.display = "table-row";
                    row.style.visibility = "visible";
                });

                const existingNoMatch = document.querySelector("#archiveStudentTable tbody .no-match-row");
                if (existingNoMatch) {
                    existingNoMatch.remove();
                }
            }
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