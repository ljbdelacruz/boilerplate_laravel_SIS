<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add School Year</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Add School Year</h1>
                <a href="{{ route('school-years.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('school-years.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf
                <div class="mb-6">
                    <label for="start_year" class="block text-gray-700 text-sm font-bold mb-2">Start Year</label>
                    <input type="number" 
                           name="start_year" 
                           id="start_year" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           min="2000" 
                           max="2099" 
                           value="{{ old('start_year', date('Y')) }}" 
                           required>
                </div>

                <div class="mb-6">
                    <label for="end_year" class="block text-gray-700 text-sm font-bold mb-2">End Year</label>
                    <input type="number" 
                           name="end_year" 
                           id="end_year" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           min="2000" 
                           max="2099" 
                           value="{{ old('end_year', date('Y') + 1) }}" 
                           required>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               class="form-checkbox h-4 w-4 text-blue-600"
                               {{ old('is_active') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Set as Active School Year</span>
                    </label>
                </div>

                <div class="flex items-center justify-end">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit">
                        Add School Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>