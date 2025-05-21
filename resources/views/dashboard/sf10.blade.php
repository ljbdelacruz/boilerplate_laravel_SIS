@extends('dashboard.admin')

@section('content')
        <div id="page-meta" data-title="View SF10 " data-parent="Students">
            <div class="max-w-6xl mx-auto pb-2">
                <div class="bg-yellow-100 shadow-sm rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">SF10 - Learner's Permanent Academic Record</h1>
                        <a href="{{ route('students.records') }}"
                            onclick="event.preventDefault(); 
                                        const schoolYearLink = [...document.querySelectorAll('.nav-link')]
                                        .find(link => link.textContent.replace(/\s+/g, ' ').trim() === 'Students'); 
                                        const title = schoolYearLink?.getAttribute('data-title') || 'Students'; 
                                        loadContent('{{ route('students.records') }}', 'Student Record Management', 'students');"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition">
                            ← Back to Student Record List
                        </a>
                    </div>

                    <!-- School Year Selector -->
                    <div class="mb-6 space-y-4">

                        <!-- School Year Selector (left-aligned) -->
                        <form method="GET" action="{{ route('teacher.student.sf10', $student->id) }}"
                            class="flex items-center space-x-3">
                            <label for="school_year_id_selector" class="text-base font-medium text-gray-800 whitespace-nowrap">
                                View Records for School Year:
                            </label>
                            <select name="school_year_id" id="school_year_id_selector" onchange="this.form.submit()"
                                class="custom-select border border-gray-300 rounded-md p-2 text-base shadow-sm focus:ring focus:ring-yellow-100 w-60">
                                @if($allSchoolYears->isEmpty())
                                    <option value="">No School Years Available</option>
                                @else
                                    @foreach($allSchoolYears as $sy)
                                        <option value="{{ $sy->id }}" {{ $selectedSchoolYearId == $sy->id ? 'selected' : '' }}>
                                            {{ $sy->start_year }} - {{ $sy->end_year }} @if($sy->is_active) (Active) @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </form>

                        <!-- Student Information -->
                        <div class="bg-yellow-50 p-5 rounded-lg shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left text-gray-800">
                                <div class="space-y-2">
                                    <p class="font-semibold text-lg">Name: {{ $student->first_name }} {{ $student->last_name }}
                                    </p>
                                    <p class="text-base">LRN: {{ $student->lrn }}</p>
                                    <p class="text-base">Grade Level: {{ $student->grade_level }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-base">School Year (Displaying):
                                        {{ $selectedSchoolYear ? $selectedSchoolYear->start_year . ' - ' . $selectedSchoolYear->end_year : 'N/A' }}
                                    </p>
                                    <p class="text-base">Section:
                                        @if($student->section && $selectedSchoolYearId == $student->school_year_id)
                                            {{ $student->section->name }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    <input type="hidden" id="current_sf10_school_year_id" value="{{ $selectedSchoolYearId }}">
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Academic Records -->
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-center">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-600 uppercase text-center">Learning Areas</th>
                                <th class="px-4 py-3 font-medium text-gray-600 uppercase text-center">Quarter 1</th>
                                <th class="px-4 py-3 font-medium text-gray-600 uppercase text-center">Quarter 2</th>
                                <th class="px-4 py-3 font-medium text-gray-600 uppercase text-center">Quarter 3</th>
                                <th class="px-4 py-3 font-medium text-gray-600 uppercase text-center">Quarter 4</th>
                                <th class="px-4 py-3 font-medium text-gray-600 uppercase text-center">Final Grade</th>
                                <th class="px-4 py-3 font-medium text-gray-600 uppercase text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-center">
                            @foreach($subjects as $subject)
                                @if($subject->children && $subject->children->count() > 0)
                                    <!-- Parent Subject Row -->
                                    <tr class="bg-gray-100 font-semibold" data-parent-row-id="{{ $subject->id }}">
                                        <td class="px-4 py-3 text-center">{{ $subject->name }}</td>
                                        <td class="px-4 py-3 parent-quarter-avg" data-quarter-avg="1"></td>
                                        <td class="px-4 py-3 parent-quarter-avg" data-quarter-avg="2"></td>
                                        <td class="px-4 py-3 parent-quarter-avg" data-quarter-avg="3"></td>
                                        <td class="px-4 py-3 parent-quarter-avg" data-quarter-avg="4"></td>
                                        <td class="px-4 py-3 parent-final-grade font-semibold"></td>
                                        <td class="px-4 py-3 parent-remarks font-semibold"></td>
                                    </tr>

                                    <!-- Sub-Subjects -->
                                    @foreach($subject->children as $childSubject)
                                        <tr data-parent-subject-id-ref="{{ $subject->id }}">
                                            <td class="px-4 py-3 text-center">{{ $childSubject->name }}</td>
                                            @for ($q = 1; $q <= 4; $q++)
                                                <td class="px-4 py-3">
                                                    <input type="number" min="0" max="100"
                                                        class="w-20 rounded border border-yellow-300 bg-yellow-50 p-1 focus:ring-yellow-200 text-center"
                                                        data-subject="{{ $childSubject->id }}" data-quarter="{{ $q }}"
                                                        value="{{ $student->grades->where('subject_id', $childSubject->id)->first()?->{$q == 1 ? 'prelim' : ($q == 2 ? 'midterm' : ($q == 3 ? 'prefinal' : 'final'))} ?? '' }}"
                                                        {{  !$selectedSchoolYear->is_active ? 'disabled' : '' }}>
                                                </td>
                                            @endfor
                                            <td class="px-4 py-3 final-grade font-semibold"></td>
                                            <td class="px-4 py-3 remarks font-semibold"></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <!-- Regular Subject Row -->
                                    <tr>
                                        <td class="px-4 py-3 text-center">{{ $subject->name }}</td>
                                        @for ($q = 1; $q <= 4; $q++)
                                            <td class="px-4 py-3">
                                                <input type="number" min="00" max="100"
                                                    class="w-20 rounded border border-yellow-300 bg-yellow-50 p-1 focus:ring-yellow-200 text-center"
                                                    data-subject="{{ $subject->id }}" data-quarter="{{ $q }}"
                                                    value="{{ $student->grades->where('subject_id', $subject->id)->first()?->{$q == 1 ? 'prelim' : ($q == 2 ? 'midterm' : ($q == 3 ? 'prefinal' : 'final'))} ?? '' }}"
                                                    {{  !$selectedSchoolYear->is_active ? 'disabled' : '' }}>
                                            </td>
                                        @endfor
                                        <td class="px-4 py-3 final-grade font-semibold"></td>
                                        <td class="px-4 py-3 remarks font-semibold"></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-6 flex justify-end space-x-4">
                        <input type="file" id="excelUpload" accept=".xlsx,.xls" class="hidden"
                            onchange="handleExcelUpload(this)">
                        <a href="{{ asset('templates/SF10_template.xlsx') }}" download
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                            Download SF10 Template
                        </a>
                        <button onclick="document.getElementById('excelUpload').click()"
                            class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg">
                            Upload Excel
                        </button>
                        <button onclick="saveGrades()"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg"
                            {{ !$selectedSchoolYear->is_active ? 'disabled' : '' }}>
                            Save Changes
                        </button>
                        <button onclick="exportToExcel()"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                            Export to Excel
                        </button>
                    </div>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
            <script>
                // Calculates final grade and remarks for a single row 
                function calculateRowGrade(row) {
                    const inputs = row.querySelectorAll('input[type="number"]');
                    let sum = 0;
                    let count = 0;

                    inputs.forEach(input => {
                        if (input.value) {
                            sum += parseFloat(input.value);
                            count++;
                        }
                    });

                    const finalGrade = count > 0 ? (sum / count).toFixed(2) : '';
                    row.querySelector('.final-grade').textContent = finalGrade;
                    row.querySelector('.remarks').textContent = finalGrade >= 75 ? 'PASSED' : finalGrade ? 'FAILED' : '';
                }

                // Calculates and updates grades for parent subjects MAPEH and ESP
                function updateAllParentSubjectGrades() {
                    document.querySelectorAll('tr[data-parent-row-id]').forEach(parentRow => {
                        const parentId = parentRow.dataset.parentRowId;
                        const subSubjectRows = document.querySelectorAll(`tr[data-parent-subject-id-ref="${parentId}"]`);

                        let quarterSums = { 1: 0, 2: 0, 3: 0, 4: 0 };
                        let quarterCounts = { 1: 0, 2: 0, 3: 0, 4: 0 };
                        let sumOfFinalGrades = 0;
                        let countOfFinalGrades = 0;

                        subSubjectRows.forEach(subRow => {
                            // Calculate quarterly averages for parent
                            for (let i = 1; i <= 4; i++) {
                                const quarterInput = subRow.querySelector(`input[data-quarter="${i}"]`);
                                if (quarterInput && quarterInput.value) {
                                    quarterSums[i] += parseFloat(quarterInput.value);
                                    quarterCounts[i]++;
                                }
                            }

                            // For overall final grade average of parent
                            const finalGradeText = subRow.querySelector('.final-grade').textContent;
                            if (finalGradeText) {
                                sumOfFinalGrades += parseFloat(finalGradeText);
                                countOfFinalGrades++;
                            }
                        });

                        // Update parent row's quarter average cells
                        for (let i = 1; i <= 4; i++) {
                            const avgQuarterCell = parentRow.querySelector(`.parent-quarter-avg[data-quarter-avg="${i}"]`);
                            if (avgQuarterCell) {
                                avgQuarterCell.textContent = quarterCounts[i] > 0 ? (quarterSums[i] / quarterCounts[i]).toFixed(2) : '';
                            }
                        }

                        // Update parent row's final grade and remarks
                        const parentFinalGradeCell = parentRow.querySelector('.parent-final-grade');
                        const parentRemarksCell = parentRow.querySelector('.parent-remarks');

                        const parentFinalGrade = countOfFinalGrades > 0 ? (sumOfFinalGrades / countOfFinalGrades).toFixed(2) : '';
                        if (parentFinalGradeCell) parentFinalGradeCell.textContent = parentFinalGrade;
                        if (parentRemarksCell) {
                            parentRemarksCell.textContent = parentFinalGrade >= 75 ? 'PASSED' : parentFinalGrade ? 'FAILED' : '';
                        }
                    });
                }

                document.querySelectorAll('input[type="number"]').forEach(input => {
                    input.addEventListener('change', (e) => {
                        calculateRowGrade(e.target.closest('tr'));
                        updateAllParentSubjectGrades();
                    });
                    calculateRowGrade(input.closest('tr'));
                });
                updateAllParentSubjectGrades();

                function saveGrades() {
                    const gradesData = [];
                    const schoolYearId = document.getElementById('current_sf10_school_year_id').value;
                    document.querySelectorAll('tbody tr').forEach(row => {
                        const subjectInput = row.querySelector('input[data-subject]');
                        if (subjectInput) {
                            const subjectId = subjectInput.dataset.subject;
                            const quarterInputs = [
                                { quarter: '1', value: row.querySelector('input[data-quarter="1"]').value },
                                { quarter: '2', value: row.querySelector('input[data-quarter="2"]').value },
                                { quarter: '3', value: row.querySelector('input[data-quarter="3"]').value },
                                { quarter: '4', value: row.querySelector('input[data-quarter="4"]').value }
                            ];
                            quarterInputs.forEach(qInput => {
                                gradesData.push({
                                    subject_id: subjectId,
                                    quarter: qInput.quarter,
                                    grade: qInput.value || null
                                });
                            });
                        }
                    });

                    fetch('/teacher/save-sf10-grades/{{ $student->id }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            grades: gradesData,
                            school_year_id: schoolYearId
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errData => {
                                    let errorMessage = 'Error saving grades.';
                                    if (errData.message) {
                                        errorMessage = errData.message;
                                    }
                                    if (errData.errors) {
                                        const errorDetails = Object.values(errData.errors).flat().join(' ');
                                        errorMessage += ' Details: ' + errorDetails;
                                    }
                                    throw new Error(errorMessage);
                                }).catch(() => {
                                    throw new Error(`Error saving grades`);
                                });
                            }
                            return response.json();
                        })

                        .then(data => {
                            const alertBox = document.createElement('div');
            alertBox.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30';
            alertBox.innerHTML = `
        <div class="bg-white rounded-md shadow max-w-xs w-full mx-4 p-4 text-center text-sm">
            <p class="text-gray-800 mb-3">${data.success ? 'Grades saved successfully!' : 'Could not save grades: ' + (data.message || data.error || 'Unknown server error.')}</p>
            <button class="px-4 py-1.5 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">OK</button>
        </div>
    `;
            alertBox.querySelector('button').onclick = () => alertBox.remove();
            document.body.appendChild(alertBox);
                        })
                        .catch(error => {
                             const alertBox = document.createElement('div');
            alertBox.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30';
            alertBox.innerHTML = `
        <div class="bg-white rounded-md shadow max-w-xs w-full mx-4 p-4 text-center text-sm">
            <p class="text-gray-800 mb-3">${error.message || 'An unexpected error occurred while saving grades.'}</p>
            <button class="px-4 py-1.5 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition">OK</button>
        </div>
    `;
            alertBox.querySelector('button').onclick = () => alertBox.remove();
            document.body.appendChild(alertBox);
                        });
                }

                function exportToExcel() {
                    window.location.href = `/teacher/export-sf10/{{ $student->id }}`;
                }

                function handleExcelUpload(input) {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('excel_file', file);
                    const schoolYearId = document.getElementById('current_sf10_school_year_id').value;
                    formData.append('selected_school_year_id', schoolYearId);

                    fetch('/teacher/handle-excel-upload/{{ $student->id }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json' 
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json() 
                                .catch(() => {
                                    // If .json() fails, it means the response was not JSON (e.g. HTML error page)
                                    return response.text().then(text => { 
                                        console.error("Server returned non-JSON response (likely HTML):", text);
                                        throw new Error(`Server returned status ${response.status}. Check console for HTML response.`);
                                    });
                                })
                                .then(errorData => { 
                                    throw { status: response.status, data: errorData }; 
                                });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            if (data.student_info) {
                                 document.querySelector('div.grid p.font-semibold').textContent = 
                                    'Name: ' + data.student_info.first_name + ' ' + data.student_info.last_name;
                            }

                            if (data.updated_grades && Array.isArray(data.updated_grades)) {
                                data.updated_grades.forEach(gradeEntry => {
                                    // Find the input for the specific subject_id (could be main or sub-subject)
                                    const subjectRow = document.querySelector(`input[data-subject="${gradeEntry.subject_id}"]`)?.closest('tr');
                                    if (subjectRow) {
                                        const prelimInput = subjectRow.querySelector('input[data-quarter="1"]');
                                        if (prelimInput && gradeEntry.prelim !== undefined) prelimInput.value = gradeEntry.prelim;

                                        const midtermInput = subjectRow.querySelector('input[data-quarter="2"]');
                                        if (midtermInput && gradeEntry.midterm !== undefined) midtermInput.value = gradeEntry.midterm;

                                        const prefinalInput = subjectRow.querySelector('input[data-quarter="3"]');
                                        if (prefinalInput && gradeEntry.prefinal !== undefined) prefinalInput.value = gradeEntry.prefinal;

                                        const finalInput = subjectRow.querySelector('input[data-quarter="4"]');
                                        if (finalInput && gradeEntry.final !== undefined) finalInput.value = gradeEntry.final;

                                        calculateRowGrade(subjectRow);
                                    }
                                });
                                updateAllParentSubjectGrades();
                                alert('Grades updated successfully from Excel file!');
                            } else if (data.message) {
                                 alert(data.message); 
                            } else {
                                alert('Excel file processed, but no specific grade updates were applied. Please check the file format and content.');
                                }
                        } else { 
                            alert('Error processing Excel file: ' + (data.message || data.error || 'Unknown error. Please check console.'));
                            if (data.error_detail) {
                                console.error("Server error detail:", data.error_detail);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error or Non-OK/Non-JSON Response:', error);
                        let alertMessage = 'Error processing Excel file. ';

                        if (error.data && error.data.message) {
                            alertMessage += error.data.message;
                            if (error.data.errors) {
                                alertMessage += " Details: " + Object.values(error.data.errors).flat().join(' ');
                            }
                        } else if (error.data && error.data.error) {
                            alertMessage += error.data.error;
                        } else if (error.message) { 
                            alertMessage += error.message;
                        } else {
                            alertMessage += 'A network or unexpected error occurred. Check console.';
                        }
                        alert(alertMessage);
                    });
                }
            }
            </script>
            <style>
                 select.custom-select {
                    appearance: none;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23333' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                    background-repeat: no-repeat;
                    background-position: right 0.55rem center;
                    background-size: 0.875rem;
                    padding-right: 2.25rem;
                 }
            </style>
        </div>
@endsection