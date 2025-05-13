@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">School Years</h1>
            <a href="{{ route('school-years.create') }}"
                onclick="event.preventDefault(); loadContent('{{ route('school-years.create') }}', 'Add School Year','school-years');"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Add School Year
            </a>
        </div>

        @if (session('success'))
            <div id="successAlert"
                class="transition-all duration-500 ease-in-out max-h-40 opacity-100 translate-y-0 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="errorAlert"
                class="transition-all duration-500 ease-in-out max-h-40 opacity-100 translate-y-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm text-center">
                {{ session('error') }}
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
                                    class="inline-block activation-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button"
                                        class="inline-block w-28 {{ $schoolYear->is_active ? 'bg-red-500 hover:bg-red-700' : 'bg-green-500 hover:bg-green-700' }} 
                   text-white font-semibold px-4 py-2 rounded-lg text-sm transition duration-200
                   {{ $schoolYear->is_active && $activeSchoolYearCount <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $schoolYear->is_active && $activeSchoolYearCount <= 1 ? 'disabled' : '' }}
                                        onclick="confirmActivation(this)">
                                        {{ $schoolYear->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <a href="{{ route('school-years.edit', $schoolYear->id) }}"
                                    onclick="event.preventDefault(); loadContent('{{ route('school-years.edit', $schoolYear->id) }}', 'Edit School Year', 'school-years');"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="activationModal" class="fixed inset-0 z-50 flex items-center justify-center hidden pointer-events-none">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center pointer-events-auto">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Confirm Activation</h3>
            <p class="text-gray-700 mb-5">Do you want to activate this school year to be active?</p>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    Cancel
                </button>
                <button id="confirmActivateBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Yes, Activate
                </button>
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
    </style>
    <script>
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

        let formToSubmit = null;

        function confirmActivation(button) {
            const form = button.closest('form');
            formToSubmit = form;
            document.getElementById('activationModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('activationModal').classList.add('hidden');
            formToSubmit = null;
        }

        document.getElementById('confirmActivateBtn').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    </script>
    </div>
    </div>
@endsection
