<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SF10 - {{ $student->first_name }} {{ $student->last_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">SF10 - Learner's Permanent Academic Record</h1>
                <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back</a>
            </div>

            <!-- Student Information -->
            <div class="mb-6 grid grid-cols-2 gap-4">
                <div>
                    <p class="font-semibold">Name: {{ $student->first_name }} {{ $student->last_name }}</p>
                    <p>LRN: {{ $student->student_id }}</p>
                    <p>Grade Level: {{ $student->grade_level }}</p>
                </div>
                <div>
                    <p>School Year: {{ $schoolYears->first()->school_year }}</p>
                    <p>Section: {{ $student->section->name }}</p>
                </div>
            </div>

            <!-- Academic Records -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Learning Areas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quarter 1</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quarter 2</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quarter 3</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quarter 4</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Final Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subjects as $subject)
                        <tr>
                            <td class="px-6 py-4">{{ $subject->name }}</td>
                            <td class="px-6 py-4">
                                <input type="number" min="60" max="100" 
                                       class="w-20 rounded border-gray-300"
                                       data-subject="{{ $subject->id }}"
                                       data-quarter="1"
                                       value="{{ $student->grades ? ($student->grades->where('subject_id', $subject->id)->first()?->prelim ?? '') : '' }}">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" min="60" max="100" 
                                       class="w-20 rounded border-gray-300"
                                       data-subject="{{ $subject->id }}"
                                       data-quarter="2"
                                       value="{{ $student->grades ? ($student->grades->where('subject_id', $subject->id)->first()?->midterm ?? '') : '' }}">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" min="60" max="100" 
                                       class="w-20 rounded border-gray-300"
                                       data-subject="{{ $subject->id }}"
                                       data-quarter="3"
                                       value="{{ $student->grades ? ($student->grades->where('subject_id', $subject->id)->first()?->prefinal ?? '') : '' }}">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" min="60" max="100" 
                                       class="w-20 rounded border-gray-300"
                                       data-subject="{{ $subject->id }}"
                                       data-quarter="4"
                                       value="{{ $student->grades ? ($student->grades->where('subject_id', $subject->id)->first()?->final ?? '') : '' }}">
                            </td>
                            <td class="px-6 py-4 final-grade"></td>
                            <td class="px-6 py-4 remarks"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <input type="file" 
                       id="excelUpload" 
                       accept=".xlsx,.xls" 
                       class="hidden" 
                       onchange="handleExcelUpload(this)">
                <button onclick="document.getElementById('excelUpload').click()" 
                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Upload Excel
                </button>
                <button onclick="saveGrades()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Save Changes
                </button>
                <button onclick="exportToExcel()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Export to Excel
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function calculateFinalGrade(row) {
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

        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('change', (e) => {
                calculateFinalGrade(e.target.closest('tr'));
            });
            calculateFinalGrade(input.closest('tr'));
        });

        function saveGrades() {
            const grades = [];
            document.querySelectorAll('tbody tr').forEach(row => {
                const subjectId = row.querySelector('input').dataset.subject;
                const values = {
                    subject_id: subjectId,
                    prelim: row.querySelector('[data-quarter="1"]').value,
                    midterm: row.querySelector('[data-quarter="2"]').value,
                    prefinal: row.querySelector('[data-quarter="3"]').value,
                    final: row.querySelector('[data-quarter="4"]').value,
                };
                grades.push(values);
            });

            fetch('/teacher/save-sf10-grades/{{ $student->id }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ grades })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Grades saved successfully!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving grades');
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

                fetch('/teacher/handle-excel-upload/{{ $student->id }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update student name
                        document.querySelector('p.font-semibold').textContent = 
                            'Name: ' + data.lastName + ' ' + data.firstName;
                        
                        // Update grades from Excel
                        const rows = document.querySelectorAll('tbody tr');
                        if (rows.length > 0) {
                            // Update Kinder 1 grades (assuming it's the first row)
                            const firstRow = rows[0];
                            if (data.kinder1_prelim) {
                                firstRow.querySelector('[data-quarter="1"]').value = data.kinder1_prelim;
                            }
                            if (data.kinder1_midterm) {
                                firstRow.querySelector('[data-quarter="2"]').value = data.kinder1_midterm;
                            }
                            if (data.kinder1_prefi) {
                                firstRow.querySelector('[data-quarter="3"]').value = data.kinder1_prefi;
                            }
                            if (data.kinder1_final) {
                                firstRow.querySelector('[data-quarter="4"]').value = data.kinder1_final;
                            }
                            // Recalculate final grade for the updated row
                            calculateFinalGrade(firstRow);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error processing Excel file');
                });
            }
        }
    </script>
</body>
</html>