@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        {{-- Title and Buttons --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Student Management</h1>
            <div class="flex flex-wrap gap-1 justify-end">
                <a href="{{ route('students.records') }}"
                    onclick="event.preventDefault(); loadContent('{{ route('students.records') }}', 'Student Record Management', 'students');"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                    Student Record Management
                </a>

                <a href="{{ route('admin.students.archivedIndex') }}"
                    onclick="event.preventDefault(); loadContent('{{ route('admin.students.archivedIndex') }}', 'View Archived Students', 'students');"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                    View Archived Students
                </a>
                <form action="{{ route('students.batch-lvlup') }}" method="POST" class="inline-block"
                    onsubmit="return confirm('Are you absolutely sure you want to promote ALL students from the CURRENT ACTIVE school year to the NEXT school year and their respective next grade levels? This action cannot be easily undone and will unassign their sections.');">
                    @csrf
                    <button type="submit"
                        class="bg-purple-500 hover:bg-purple-600 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                    Batch Move Up Students (Active SY)
                    </button>
                </form>
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

        {{-- Table Card --}}
        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
            {{-- Header: Filter + Search --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 gap-4">
                {{-- Left: Filter + Search --}}
                <div class="flex items-center space-x-2">
                    {{-- School Year Filter --}}
                    <form method="GET" action="{{ route('students.index') }}" class="flex items-center gap-2">
                        <div class="w-44">
                            <select name="school_year_id" id="school_year_id" onchange="this.form.submit()"
                                class="custom-select bg-white border border-gray-300 text-sm rounded-lg py-2 px-3 w-full focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Years</option>
                                @foreach($schoolYears as $sy)
                                    <option value="{{ $sy->id }}" {{ (string) $selectedSchoolYearId === (string) $sy->id ? 'selected' : '' }}>
                                        {{ $sy->start_year }} - {{ $sy->end_year }} {{ $sy->is_active ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    {{-- Search Bar --}}
                    <div class="relative w-72">
                        <img src="{{ asset('icons/searchlogo.png') }}" alt="Search Icon"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4">
                        <input type="text" id="searchStudent" placeholder="Search student by first or last name..."
                            class="bg-gray-100 text-sm text-gray-700 pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Filter Button --}}
                    <button onclick="filterStudents()" id="filterButton"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                        Search
                    </button>

                    {{-- Clear Button --}}
                    <button onclick="clearSearch()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded-lg text-sm">
                        Clear
                    </button>
                </div>

                {{-- Right: Add Student + View Archive --}}
                <div class="flex flex-wrap gap-2 justify-end">
                    <a href="{{ route('students.upload') }}"
                        onclick="event.preventDefault(); loadContent('{{ route('students.upload') }}', 'Batch Upload', 'students');"
                        class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                        Batch Upload
                    </a>

                    <a href="{{ route('students.create') }}"
                        onclick="event.preventDefault(); loadContent('{{ route('students.create') }}', 'Add Student','students');"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Add Student
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="w-full overflow-x-auto">
                <table id="studentsTable" class="min-w-full text-center text-sm divide-y divide-gray-300 table-fixed">
                    <thead class="bg-yellow-100">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">LRN</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Student ID</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Last Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">First Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">School Year</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Grade Level</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Section</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($students->isEmpty())
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No students available for the active school year.
                                </td>
                            </tr>
                        @else
                            @foreach ($students as $student)
                                <tr class="hover:bg-yellow-50 transition-colors duration-200"
                                    data-school-year-id="{{ $student->schoolYear->id }}">
                                    <td class="px-6 py-4 font-medium text-gray-700">{{ $student->lrn }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-700">{{ $student->student_id }}</td>
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $student->last_name }}</td>
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap">{{ $student->first_name }}</td>
                                    <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                        {{ $student->schoolYear->start_year }} - {{ $student->schoolYear->end_year }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $student->grade_level }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $student->section->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex flex-col">
                                            {{-- First row: View and Edit --}}
                                            <div class="flex justify-center space-x-2 mb-1">
                                                <a href="{{ route('students.show', $student) }}"
                                                    onclick="event.preventDefault(); loadContent('{{ route('students.show', $student) }}', 'View Student','students');"
                                                    class="w-20 bg-blue-500 hover:bg-blue-600 text-white font-semibold px-3 py-2 rounded-lg text-sm text-center">
                                                    View
                                                </a>
                                                <a href="{{ route('students.edit', $student) }}"
                                                    onclick="event.preventDefault(); loadContent('{{ route('students.edit', $student) }}', 'Edit Student','students');"
                                                    class="w-20 bg-green-500 hover:bg-green-600 text-white font-semibold px-3 py-2 rounded-lg text-sm text-center">
                                                    Edit
                                                </a>
                                            </div>

                                            {{-- Second row: Level Up and Archive --}}
                                            @if (Auth::user()->role === 'admin' || Auth::user()->role === 'teacher')
                                                <div class="flex justify-center space-x-2">
                                                    <form action="{{ route('students.lvlup', $student->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to level up {{ $student->first_name }} {{ $student->last_name }}?');">
                                                        @csrf
                                                        <button type="submit"
                                                            class="w-20 bg-purple-500 hover:bg-purple-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                                            Move Up
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('students.destroy', $student) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to archive this student?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-20 bg-red-500 hover:bg-red-600 text-white font-semibold px-3 py-2 rounded-lg text-sm">
                                                            Archive
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6">
                {{ $students->links() }}
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
            function filterStudents() {
                const searchInput = document.getElementById("searchStudent").value.trim().toLowerCase();
                const selectedSchoolYearId = document.getElementById("school_year_id").value;

                const rows = document.querySelectorAll("#studentsTable tbody tr:not(.no-match-row)");
                let found = false;

                rows.forEach(row => {
                    const lastName = row.children[2].textContent.toLowerCase();
                    const firstName = row.children[3].textContent.toLowerCase();
                    const studentSchoolYearId = row.getAttribute("data-school-year-id"); // Assuming you add school year ID to each row

                    // Check if the student matches both the search input and the selected school year
                    const matchesSearch = lastName.includes(searchInput) || firstName.includes(searchInput);
                    const matchesSchoolYear = selectedSchoolYearId === "" || studentSchoolYearId === selectedSchoolYearId;

                    // Only show the row if both conditions are met
                    if (matchesSearch && matchesSchoolYear) {
                        row.style.display = "";
                        row.style.visibility = "visible"; // Prevent layout shift
                        found = true;
                    } else {
                        row.style.display = "none";
                        row.style.visibility = "collapse";
                    }
                });

                const tbody = document.querySelector("#studentsTable tbody");
                let noMatchRow = tbody.querySelector(".no-match-row");

                if (!found) {
                    if (!noMatchRow) {
                        noMatchRow = document.createElement("tr");
                        noMatchRow.classList.add("no-match-row");
                        noMatchRow.innerHTML = `
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 bg-white text-sm">
                                No students match the search and filter criteria.
                            </td>
                            `;
                        tbody.appendChild(noMatchRow);
                    }
                } else if (noMatchRow) {
                    noMatchRow.remove();
                }
            }

            function clearSearch() {
                document.getElementById("searchStudent").value = "";

                const selectedSchoolYearId = document.getElementById("school_year_id").value;
                const rows = document.querySelectorAll("#studentsTable tbody tr:not(.no-match-row)");
                let found = false;

                rows.forEach(row => {
                    const lastName = row.children[2].textContent.toLowerCase();
                    const firstName = row.children[3].textContent.toLowerCase();
                    const studentSchoolYearId = row.getAttribute("data-school-year-id");

                    const matchesSchoolYear = selectedSchoolYearId === "" || studentSchoolYearId === selectedSchoolYearId;

                    if (matchesSchoolYear) {
                        row.style.display = "table-row";
                        row.style.visibility = "visible";
                        found = true;
                    } else {
                        row.style.display = "none";
                        row.style.visibility = "collapse";
                    }
                });

                const existingNoMatch = document.querySelector("#studentsTable tbody .no-match-row");
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