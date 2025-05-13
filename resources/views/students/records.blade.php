@extends('dashboard.admin')

@section('content')
    <div id="page-meta" data-title="Student Record Management" data-parent="Students">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Manage Student Records</h1>
                <div>
                    <a href="{{ route('students.index') }}" onclick="event.preventDefault(); 
                                                    const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                                       .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                                                   const title = schoolYearLink?.getAttribute('data-title') || 'Students'; 
                                                    loadContent('{{ route('students.index') }}', title, 'students');"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                        ← Back to Student List
                    </a>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <!-- Search Bar -->
                        <div class="relative w-72">
                            <img src="{{ asset('icons/searchlogo.png') }}" alt="Search Icon"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4">
                            <input type="text" id="searchRecordStudent" placeholder="Search for students by name..."
                                class="bg-gray-100 text-sm text-gray-700 pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Filter Button -->
                        <button onclick="filterRecordStudents()" id="filterButton"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                            Search
                        </button>

                        <!-- Clear Button -->
                        <button onclick="clearRecordSearch()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded-lg text-sm">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table id="RecordTable" class="min-w-full text-center text-sm divide-y divide-gray-300">
                        <thead class="bg-yellow-100">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">LRN</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Student ID
                                </th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Grade Level
                                </th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Section</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $student)
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    <td class="px-6 py-4 font-medium text-gray-700">{{ $student->lrn }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-700">{{ $student->student_id }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $student->last_name }}, {{ $student->first_name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $student->grade_level }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $student->section->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('teacher.student.sf10', $student->id) }}"
                                            onclick="event.preventDefault(); loadContent('{{ route('teacher.student.sf10', $student->id) }}', 'View SF10','students');"
                                            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                                            SF10
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="px-6">
                        {{ $students->links() }}
                    </div>
                </div>
                <script>
                    function filterRecordStudents() {
                        const input = document.getElementById("searchRecordStudent").value.trim().toLowerCase();
                        const rows = document.querySelectorAll("#RecordTable tbody tr:not(.no-match-row)");
                        let found = false;

                        rows.forEach(row => {
                            const nameCell = row.children[2]; // Name is the 3rd column (index 2)
                            const nameText = nameCell.textContent.trim().toLowerCase();
                            const match = nameText.includes(input);

                            row.style.display = match ? "table-row" : "table-row";
                            row.style.visibility = match ? "visible" : "collapse";

                            if (match) found = true;
                        });

                        const tbody = document.querySelector("#RecordTable tbody");
                        let noMatchRow = tbody.querySelector(".no-match-row");

                        if (!found) {
                            if (!noMatchRow) {
                                noMatchRow = document.createElement("tr");
                                noMatchRow.classList.add("no-match-row");
                                noMatchRow.innerHTML = `
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 bg-white text-sm">
                            No student matches found
                        </td>`;
                                tbody.appendChild(noMatchRow);
                            }
                        } else if (noMatchRow) {
                            noMatchRow.remove();
                        }
                    }

                    function clearRecordSearch() {
                        document.getElementById("searchRecordStudent").value = "";
                        const rows = document.querySelectorAll("#RecordTable tbody tr:not(.no-match-row)");
                        rows.forEach(row => {
                            row.style.display = "table-row";
                            row.style.visibility = "visible";
                        });

                        const existingNoMatch = document.querySelector("#RecordTable tbody .no-match-row");
                        if (existingNoMatch) {
                            existingNoMatch.remove();
                        }
                    }
                </script>
            </div>
        </div>
    </div>

@endsection