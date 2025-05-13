@extends('dashboard.admin')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Schedule Management</h1>
            <div class="flex space-x-2">
                <a href="{{ route('schedules.create') }}"
                    onclick="event.preventDefault(); loadContent('{{ route('schedules.create') }}', 'Add Schedule','schedules');"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Add Schedule
                </a>
                <a href="{{ route('schedules.auto-generate-form') }}"
                    onclick="event.preventDefault(); loadContent('{{ route('schedules.auto-generate-form') }}', 'Generate Schedule','schedules');"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                    Generate Schedules
                </a>
            </div>
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

        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full text-center text-sm divide-y divide-gray-300">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($schedules->isEmpty())
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No schedules available for the active school year.
                            </td>
                        </tr>
                    @else
                        @foreach($schedules as $schedule)
                            <tr>
                            <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-gray-800">{{ $schedule->teacher->name }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $schedule->course->name }}
                                    ({{ $schedule->course->grade_level }})</td>
                                <td class="px-6 py-4 text-gray-700">{{ $schedule->section->name }}
                                    ({{ $schedule->section->grade_level }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('schedules.edit', $schedule) }}"
                                        onclick="event.preventDefault(); loadContent('{{ route('schedules.edit', $schedule) }}', 'Edit Schedule','schedules');"
                                        class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                                        Edit
                                    </a>
                                    <button type="button"
                                        onclick="showCenteredDeletePopup('{{ route('schedules.destroy', $schedule) }}')"
                                        class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-sm">
                                        Delete
                                    </button>
                                    <form id="deleteFormTemplate" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')

                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showCenteredDeletePopup(actionUrl) {
            // Remove existing popups
            document.querySelectorAll('.delete-popup').forEach(p => p.remove());

            // Create the popup
            const popup = document.createElement('div');
            popup.className =
                'delete-popup fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white shadow-lg p-6 rounded-lg border border-gray-300 z-50 text-center w-80';
            popup.innerHTML = `
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Delete Schedule</h2>
                        <p class="text-sm text-gray-700 mb-4">Are you sure you want to delete this schedule?</p>
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
@endsection