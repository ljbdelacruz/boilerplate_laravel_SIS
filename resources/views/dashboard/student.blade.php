<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | Ususan Elementary School</title>
    <link rel="icon" href="{{ asset('icons/Logo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

     <style>
        #dropdownMenu {
            min-width: 7rem;
            background-color: #e6db8b;
            border-radius: 0.6rem;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.16);
            z-index: 50;
            align-items: center;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        #dropdownMenu.show {
            opacity: 1;
            pointer-events: auto;
        }

        #dropdownMenu .hover-red:hover {
            color: red;
        }

        #dropdownToggle {
            transition: opacity 0.3s ease;
        }

        #dropdownToggle:hover {
            transform: scale(1.1);
        }

         @media (max-width: 1024px) {
      
        .logo-img {
            height: 2.25rem; 
            width: 2.25rem;
        }

        .school-name {
            font-size: 1rem; 

        .user-info span:first-child {
            font-size: 0.625rem; 
        }

        .user-info span:last-child {
            font-size: 0.875rem; 
        }

        #dropdownToggle {
            width: 1.5rem; 
            height: 1.5rem;
        }

        #dropdownToggle svg {
            width: 1.25rem;
            height: 1.25rem;
        }
    }
}

    @media (max-width: 640px) {
        .logo-img {
            height: 2rem; 
            width: 2rem;
        }

        .school-name {
            font-size: 0.875rem; 
        }

        .user-info span:first-child {
            font-size: 0.5rem; 
        }

        .user-info span:last-child {
            font-size: 0.75rem; 
        }

        #dropdownToggle {
            width: 1.25rem; 
            height: 1.25rem;
        }

        #dropdownToggle svg {
            width: 1rem;
            height: 1rem;
        }
    }

    @media (max-width: 400px) {
        .school-name {
            display: none;
        }
    }
    </style>
</head>
<body class="bg-gray-100">
   <nav class="relative bg-gray-50 h-16 px-4 flex items-center justify-between sticky top-0 z-40 shadow-lg"
        style="background-color: #EAD180; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);">
        <!-- Title -->
        <div class="flex items-center gap-2">
            <!-- Removed burger icon -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('icons/Logo.png') }}" alt="Ususan Logo" class="h-10 w-10 object-contain logo-img flex-shrink-0">
                <span class="font-bold text-lg text-gray-900 school-name">Ususan Elementary School</span>
            </div>
        </div>

        {{-- Right: User Info + Dropdown (unchanged) --}}
        <div class="flex items-center gap-4 relative">
            <!-- User Info -->
            <div class="flex flex-col items-end leading-tight user-info">
                <span class="text-xs text-blue-500 font-semibold">STUDENT</span>
                <span class="font-bold text-[20px]">{{ Auth::user()->name }}</span>
            </div>

            <div class="relative">
                <button id="dropdownToggle"
                    class="flex items-center justify-center w-7 h-7 rounded-full transition-transform duration-200 transform hover:scale-110 focus:outline-none"
                    style="background-color: #000000; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);">
                    <svg class="w-5 h-5 transition-colors duration-200" fill="none" stroke="currentColor"
                        stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="color: #ffffff;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="dropdownMenu"
                    class="absolute right-0 top-9 mt-2 border border-gray-300 shadow-lg z-50 opacity-0 pointer-events-none transition-opacity duration-300">
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-[#e6db8b] hover-red font-bold">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 mb-4">My Profile</h2>
                <div class="space-y-2 text-gray-600 text-lg">
                    <p><span class="font-semibold">Name:</span> {{ Auth::user()->name }}</p>
                    <p><span class="font-semibold">Email:</span> {{ Auth::user()->email }}</p>                    
                    @if(Auth::user()->student)
                        <p><span class="font-semibold">Sex:</span> {{ Auth::user()->student->gender ? ucfirst(Auth::user()->student->gender) : 'N/A' }}</p>
                        <p><span class="font-semibold">LRN:</span> {{ Auth::user()->student->lrn ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Grade Level:</span> {{ Auth::user()->student->grade_level ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Section:</span> {{ Auth::user()->student->section->name ?? 'N/A' }}</p>
                    @endif
                </div>
            </div>

            <!-- Enrolled Subjects -->
            <div class="bg-[#fdfdfd] shadow-lg rounded-2xl p-6 md:col-span-2 border border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 mb-4">My Subjects & Grades</h2>
                @php
                    $student = Auth::user()->student;
                    $subjects = collect(); 
                    $studentGrades = collect();
                    $currentSchoolYear = null;

                    if ($student && $student->schoolYear) { 
                        $currentSchoolYear = $student->schoolYear;
                    }

                    if ($student && $currentSchoolYear && $student->grade_level) {
                        $subjects = \App\Models\Course::whereNull('parent_id') 
                                                      ->where('grade_level', $student->grade_level)
                                                      ->where('is_active', true) 
                                                      ->with(['children' => function ($query) {
                                                          $query->where('is_active', true)->orderBy('id');
                                                      }])
                                                      ->orderBy('id')
                                                      ->get();
                        
                        $studentGrades = \App\Models\Grade::where('student_id', $student->id)
                                                          ->where('school_year_id', $currentSchoolYear->id)
                                                          ->get()
                                                          ->keyBy('subject_id');
                    }
                @endphp

                @if(!$currentSchoolYear)
                    <p class="text-gray-700 text-base">Your current school year information is not available to display grades.</p>
                @elseif($subjects->count() > 0)
                    <p class="text-sm text-gray-500 mb-4 italic">Displaying grades for School Year: <span class="font-medium text-gray-700">{{ $currentSchoolYear->start_year }} - {{ $currentSchoolYear->end_year }}</span></p>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-300 text-[15px]">
                            <thead class="bg-[#f9fafb] text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q1</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q2</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q3</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q4</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Final Rating</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subjects as $parentCourse)
                                    @php
                                        $parentGradesEntry = $studentGrades->get($parentCourse->id);
                                        $parentFinalGrade = null;
                                        $parentRemarks = '';
                                        $parentQ = ['prelim' => null, 'midterm' => null, 'prefinal' => null, 'final' => null];

                                        if ($parentCourse->children && $parentCourse->children->count() > 0) {
                                            $childFinalGradesSum = 0; $childFinalGradesCount = 0;
                                            $childQuarterSums = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];
                                            $childQuarterCounts = ['prelim' => 0, 'midterm' => 0, 'prefinal' => 0, 'final' => 0];

                                            foreach ($parentCourse->children as $childCourse) {
                                                $childGradeEntry = $studentGrades->get($childCourse->id);
                                                if ($childGradeEntry) {
                                                    $quarters = ['prelim', 'midterm', 'prefinal', 'final'];
                                                    $childNumericGrades = [];
                                                    foreach ($quarters as $qKey) {
                                                        if (is_numeric($childGradeEntry->$qKey)) {
                                                            $childQuarterSums[$qKey] += $childGradeEntry->$qKey;
                                                            $childQuarterCounts[$qKey]++;
                                                            $childNumericGrades[] = $childGradeEntry->$qKey;
                                                        }
                                                    }
                                                    if (count($childNumericGrades) > 0) {
                                                        $childFinal = round(array_sum($childNumericGrades) / count($childNumericGrades));
                                                        $childFinalGradesSum += $childFinal;
                                                        $childFinalGradesCount++;
                                                    }
                                                }
                                            }
                                            foreach (['prelim', 'midterm', 'prefinal', 'final'] as $qKey) {
                                                $parentQ[$qKey] = $childQuarterCounts[$qKey] > 0 ? round($childQuarterSums[$qKey] / $childQuarterCounts[$qKey]) : null;
                                            }
                                            if ($childFinalGradesCount > 0) {
                                                $parentFinalGrade = round($childFinalGradesSum / $childFinalGradesCount);
                                                $parentRemarks = $parentFinalGrade >= 75 ? 'Passed' : 'Failed';
                                            }
                                        } elseif ($parentGradesEntry) {
                                            $parentQ['prelim'] = $parentGradesEntry->prelim; $parentQ['midterm'] = $parentGradesEntry->midterm;
                                            $parentQ['prefinal'] = $parentGradesEntry->prefinal; $parentQ['final'] = $parentGradesEntry->final;
                                            $numericGrades = array_filter([$parentGradesEntry->prelim, $parentGradesEntry->midterm, $parentGradesEntry->prefinal, $parentGradesEntry->final], 'is_numeric');
                                            if (count($numericGrades) > 0) {
                                                $parentFinalGrade = round(array_sum($numericGrades) / count($numericGrades));
                                                $parentRemarks = $parentFinalGrade >= 75 ? 'Passed' : 'Failed';
                                            }
                                        }
                                    @endphp
                                    <tr class="font-medium bg-gray-50">
                                        <td class="px-4 py-3">{{ $parentCourse->name }}</td>
                                        <td class="px-4 py-3 text-center">{{ $parentQ['prelim'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $parentQ['midterm'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $parentQ['prefinal'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $parentQ['final'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $parentFinalGrade ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $parentRemarks }}</td>
                                    </tr>

                                    @if($parentCourse->children && $parentCourse->children->count() > 0)
                                        @foreach($parentCourse->children as $childCourse)
                                            @php
                                                $childGradesEntry = $studentGrades->get($childCourse->id);
                                                $childFinalGrade = null; $childRemarks = '';
                                                $childQ = ['prelim'=>null,'midterm'=>null,'prefinal'=>null,'final'=>null];
                                                if ($childGradesEntry) {
                                                    $childQ['prelim']=$childGradesEntry->prelim; $childQ['midterm']=$childGradesEntry->midterm;
                                                    $childQ['prefinal']=$childGradesEntry->prefinal; $childQ['final']=$childGradesEntry->final;
                                                    $numericGrades = array_filter([$childGradesEntry->prelim, $childGradesEntry->midterm, $childGradesEntry->prefinal, $childGradesEntry->final], 'is_numeric');
                                                    if (count($numericGrades) > 0) {
                                                        $childFinalGrade = round(array_sum($numericGrades) / count($numericGrades));
                                                        $childRemarks = $childFinalGrade >= 75 ? 'Passed' : 'Failed';
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td class="pl-8 pr-4 py-3">{{ $childCourse->name }}</td>
                                                <td class="px-4 py-3 text-center">{{ $childQ['prelim'] ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center">{{ $childQ['midterm'] ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center">{{ $childQ['prefinal'] ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center">{{ $childQ['final'] ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center">{{ $childFinalGrade ?? '-' }}</td>
                                                <td class="px-4 py-3 text-center">{{ $childRemarks }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                                <tr>
                                    <td colspan="7" class="px-5 py-4 text-center text-sm text-gray-500 bg-gray-50 italic">
                                        <strong>Descriptors:</strong> 90-100 (Outstanding); 85-89 (Very Satisfactory); 80-84 (Satisfactory); 75-79 (Fairly Satisfactory); Below 75 (Did Not Meet Expectations)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-700 text-base">No subjects or grades are currently assigned for your grade level in the school year {{ $currentSchoolYear ? ($currentSchoolYear->start_year . ' - ' . $currentSchoolYear->end_year) : '' }}.</p>
                @endif
            </div>
            <!-- Learner's Observed Values -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-lg p-6 md:col-span-3 mt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Learner's Observed Values (SF9-ES)</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-sm text-gray-700">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-1/4">Core Value</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider w-2/4">Behavioral Statements</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q1</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q2</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q3</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Q4</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td rowspan="1" class="px-4 py-3 align-top font-medium border-r">Maka-Diyos</td>
                                <td class="px-4 py-3">Expresses one's spiritual beliefs while respecting the spiritual beliefs of others</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                            </tr>
                            <tr>
                                <td rowspan="1" class="px-4 py-3 align-top font-medium border-r">Makatao</td>
                                <td class="px-4 py-3">Shows adherence to ethical principles by upholding truth.</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                            </tr>
                             <tr>
                                <td class="px-4 py-3 align-top font-medium border-r">Makakalikasan</td>
                                <td class="px-4 py-3">Cares for the environment and utilizes resources wisely, judiciously, and economically.</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="px-4 py-3 align-top font-medium border-r">Makabansa</td>
                                <td class="px-4 py-3">Demonstrates pride in being a Filipino; exercises the rights and responsibilities of a Filipino citizen.</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3">Demonstrates appropriate behavior in carrying out activities in the school, community, and country.</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                                <td class="px-4 py-3 text-center">-</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500 text-xs bg-gray-50 italic">
                                    <strong>Marking:</strong> AO - Always Observed, SO - Sometimes Observed, RO - Rarely Observed, NO - Not Observed
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const toggleButton = document.getElementById('dropdownToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');

        toggleButton.addEventListener('click', function (e) {
            e.preventDefault();
            dropdownMenu.classList.toggle('show');
        });

        window.addEventListener('click', function (e) {
            if (!toggleButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>
</body>
</html>