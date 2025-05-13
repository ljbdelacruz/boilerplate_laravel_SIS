@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Curriculum Management</h1>
        </div>

        @if(session('success'))
            <div id="successAlert"
                class="transition-all duration-500 ease-in-out max-h-40 opacity-100 translate-y-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div id="errorAlert"
                class="transition-all duration-500 ease-in-out max-h-40 opacity-100 translate-y-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('error') }}
            </div>
        @endif

        @if (!$activeSchoolYear)
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4">
                No active school year found.
            </div>
        @else
            <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
                <!-- Top Row -->
                <div class="flex flex-wrap items-center justify-between px-6 pt-6 pb-4 gap-4">
                    <!-- Active School Year -->
                    <div class="flex-1 min-w-[200px]">
                        <span class="text-lg font-bold text-gray-700">Active School Year:</span>
                        <span class="text-xl text-gray-900 ml-2">
                            {{ $activeSchoolYear->getSchoolYearDisplayAttribute() }}
                        </span>
                    </div>

                    <!-- Section Dropdown -->
                    <div class="flex-1 text-center min-w-[220px]">
                        <select name="section_id" id="section_id"
                            class="custom-select w-64 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="" disabled selected>Select a section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-wrap justify-end gap-2 min-w-[200px]">
                        <button id="view-curriculum"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-lg transition duration-200">
                            View Curriculum
                        </button>
                        <a href="{{ route('curriculums.create') }}"
                            onclick="event.preventDefault(); loadContent('{{ route('curriculums.create') }}', 'Add Curriculum','curriculums');"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg">
                            Add Curriculum
                        </a>
                    </div>
                </div>

                <!-- Section Error Message (Centered below row) -->
                <div class="px-6 pb-4">
                    <p id="section-error"
                        class="text-red-500 text-sm text-center opacity-0 max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        Please select a section to view the curriculum.
                    </p>
                </div>
            </div>

            <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200 mt-10">
                <div id="curriculum-table-container" class="mt-6">
                    <h2 class="text-xl font-semibold mb-6">
                        Curriculum Details for <span id="section-name"></span>
                    </h2>
                    <div class="overflow-x-auto">
                        <table id="curriculum-table"
                            class="min-w-full table-fixed text-sm divide-y divide-gray-300 text-center">
                            <thead class="bg-yellow-100">
                                <tr>
                                    <th
                                        class="w-1/4 px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider text-center">
                                        Subject</th>
                                    <th
                                        class="w-1/4 px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider text-center">
                                        Start Time</th>
                                    <th
                                        class="w-1/4 px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider text-center">
                                        End Time</th>
                                    <th
                                        class="w-1/4 px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider text-center">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-center">
                                <tr id="no-data-row">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No curriculum available, Select section to view curriculum
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <form id="deleteFormTemplate" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

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

        #section-error {
            transition: opacity 0.5s ease, max-height 0.5s ease;
        }

         select.custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 0.875rem;
            padding-right: 2.25rem;
         }
    </style>

    <script>
        function initCurriculumView() {
            const sectionDropdown = document.getElementById('section_id');
            const viewCurriculumButton = document.getElementById('view-curriculum');
            const curriculumTable = document.getElementById('curriculum-table');
            const sectionNameSpan = document.getElementById('section-name');
            const errorMessage = document.getElementById('section-error');

            if (!viewCurriculumButton || !sectionDropdown) return;

            viewCurriculumButton.addEventListener('click', function () {
                const selectedSectionId = sectionDropdown.value;

                // Hide the error first
                errorMessage.classList.remove('opacity-100', 'max-h-10');
                errorMessage.classList.add('opacity-0', 'max-h-0');

                if (!selectedSectionId) {
                    // Show the error
                    errorMessage.classList.remove('opacity-0', 'max-h-0');
                    errorMessage.classList.add('opacity-100', 'max-h-10');

                    // Hide after 3 seconds
                    setTimeout(() => {
                        errorMessage.classList.remove('opacity-100', 'max-h-10');
                        errorMessage.classList.add('opacity-0', 'max-h-0');
                    }, 3000);
                    return;
                }

                // Proceed to load
                sectionNameSpan.textContent = '...';
                const tbody = curriculumTable.querySelector('tbody');
                tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-5 text-gray-500 text-sm text-center">Loading curriculum...</td>
                </tr>
            `;

                loadCurriculum(selectedSectionId);
            });


            function loadCurriculum(sectionId) {
                fetch(`/sections/${sectionId}/curriculum`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to fetch curriculum.');
                        return response.json();
                    })
                    .then(data => {
                        const tbody = curriculumTable.querySelector('tbody');
                        tbody.innerHTML = '';
                        sectionNameSpan.textContent = data.section_name;

                        if (data.error) {
                            tbody.innerHTML = `
                            <tr>
                                <td colspan="4" class="px-6 py-5 text-sm text-red-500 text-center">${data.error}</td>
                            </tr>`;
                        } else if (data.curriculum && data.curriculum.length > 0) {
                            data.curriculum.forEach(item => {
                                tbody.innerHTML += `
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    <td class="w-1/4 px-6 py-3 border-b border-gray-200 text-sm text-gray-800 text-center">
                                        ${item.subject_name} (${item.grade_level})
                                    </td>
                                    <td class="w-1/4 px-6 py-3 border-b border-gray-200 text-sm text-gray-800 text-center">
                                        ${item.start_time}
                                    </td>
                                    <td class="w-1/4 px-6 py-3 border-b border-gray-200 text-sm text-gray-800 text-center">
                                        ${item.end_time}
                                    </td>
                                    <td class="w-1/4 px-6 py-3 border-b border-gray-200 text-sm text-center space-x-2">
        <a href="/curriculums/${item.id}/edit"
           onclick="event.preventDefault(); loadContent('/curriculums/${item.id}/edit', 'Edit Curriculum', 'curriculums');"
           class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition duration-200">
           Edit
        </a>
        <button
            onclick="showCenteredDeletePopup('/curriculums/${item.id}')"
            class="inline-block bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition duration-200">
            Delete
        </button>
    </td>
                                </tr>`;
                            });
                        } else {
                            tbody.innerHTML = `
                            <tr>
                                <td colspan="4" class="px-6 py-5 text-sm text-gray-500 text-center">No curriculum available</td>
                            </tr>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching curriculum:', error);
                        const tbody = curriculumTable.querySelector('tbody');
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-6 py-5 text-sm text-red-500 text-center">Error: ${error.message}</td>
                        </tr>`;
                    });
            }
        }

        // Delete popup used across pages
        function showCenteredDeletePopup(actionUrl) {
            document.querySelectorAll('.delete-popup').forEach(p => p.remove());

            const popup = document.createElement('div');
            popup.className =
                'delete-popup fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white shadow-lg p-6 rounded-lg border border-gray-300 z-50 text-center w-80';
            popup.innerHTML = `
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Delete Curriculum</h2>
            <p class="text-sm text-gray-700 mb-4">Are you sure you want to delete this curriculum entry?</p>
            <div class="flex justify-center gap-4">
                <button class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm rounded" onclick="this.closest('.delete-popup').remove()">Cancel</button>
                <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded" onclick="submitDelete('${actionUrl}')">Delete</button>
            </div>
        `;
            document.body.appendChild(popup);
        }

        function submitDelete(actionUrl) {
            const form = document.getElementById('deleteFormTemplate').cloneNode(true);
            form.action = actionUrl;
            form.style.display = 'none';
            document.body.appendChild(form);

            // Close the confirmation popup
            document.querySelectorAll('.delete-popup').forEach(p => p.remove());

            form.submit();
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
@endsection
