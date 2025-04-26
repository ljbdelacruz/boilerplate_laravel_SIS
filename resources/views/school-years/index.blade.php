@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">School Years</h1>
            <a href="{{ route('school-years.create') }}"
                onclick="event.preventDefault(); loadContent('{{ route('school-years.create') }}', 'Add School Year');"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Add School Year
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full text-center text-sm divide-y divide-gray-300">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">School Year</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($schoolYears as $schoolYear)
                        <tr class="hover:bg-yellow-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $schoolYear->school_year_display }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                            {{ $schoolYear->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $schoolYear->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <form action="{{ route('school-years.toggle-active', $schoolYear) }}" method="POST"
                                    class="inline-block">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="inline-block w-28 {{ $schoolYear->is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white font-semibold px-4 py-2 rounded-lg text-sm transition duration-200">
                                        {{ $schoolYear->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <a href="{{ route('school-years.edit', $schoolYear->id) }}"
                                onclick="event.preventDefault(); loadContent('{{ route('school-years.edit', $schoolYear->id) }}', 'Edit School Year');"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">
                                Edit
                            </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        

            <style>
                .animate-fadeIn {
                    animation: fadeIn 0.3s ease-out;
                }

                .animate-fadeOut {
                    animation: fadeOut 0.3s ease-in;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes fadeOut {
                    from {
                        opacity: 1;
                        transform: translateY(0);
                    }

                    to {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                }
            </style>
        </div>
    </div>
@endsection
