<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Submit Grades for {{ $student->first_name }} {{ $student->last_name }}</h2>
                <a href="{{ route('teacher.view.students') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back
                </a>
            </div>

            <form id="gradesForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prelim</label>
                        <input type="number" name="prelim" min="0" max="100" step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               value="{{ $grades->prelim ?? '' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Midterm</label>
                        <input type="number" name="midterm" min="0" max="100" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               value="{{ $grades->midterm ?? '' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pre-Final</label>
                        <input type="number" name="prefinal" min="0" max="100" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               value="{{ $grades->prefinal ?? '' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Final</label>
                        <input type="number" name="final" min="0" max="100" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               value="{{ $grades->final ?? '' }}">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="submitGrades()" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Save Grades
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function submitGrades() {
            const formData = {
                prelim: document.querySelector('input[name="prelim"]').value,
                midterm: document.querySelector('input[name="midterm"]').value,
                prefinal: document.querySelector('input[name="prefinal"]').value,
                final: document.querySelector('input[name="final"]').value
            };

            fetch('/teacher/save-grades/{{ $student->id }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Grades saved successfully!');
                    window.location.href = '{{ route("teacher.view.students") }}';
                } else {
                    alert('Error saving grades');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving grades. Please try again.');
            });
        }
    </script>
</body>
</html>