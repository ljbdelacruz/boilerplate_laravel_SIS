<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Upload Teachers</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                <span class="inline-flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </span>
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Batch Upload Teachers</h2>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <form id="uploadForm" action="{{ route('teachers.upload.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="file">
                            Excel File (.xlsx, .xls)
                        </label>
                        <input type="file" name="file" id="file" accept=".xlsx,.xls" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <a href="{{ asset('templates/teacher_template.xlsx') }}" class="text-blue-500 hover:text-blue-700">
                            Download Template
                        </a>
                    </div>

                    <button type="button" onclick="showConfirmation()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Upload Teachers
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Upload</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to upload this file? This will create new teacher accounts.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmUpload" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Yes, Upload
                    </button>
                    <button onclick="hideConfirmation()" class="mt-3 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showConfirmation() {
            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        function hideConfirmation() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }

        document.getElementById('confirmUpload').addEventListener('click', function() {
            document.getElementById('uploadForm').submit();
        });
    </script>
</body>
</html>