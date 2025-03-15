<!-- filepath: /Users/laineljohn/Desktop/projects/php/cs2/capstone2_laravel/resources/views/school_years.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Years</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>


    <div class="container">
        <h1>School Years</h1>
        @if (empty($schoolYears))
            <p>No school years found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>School Year</th>
                        <th>Grade Level</th>
                        <th>Section Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schoolYears as $schoolYear)
                        <tr>
                            <td>{{ $schoolYear->school_year }}</td>
                            <td>{{ $schoolYear->grade_level }}</td>
                            <td>{{ $schoolYear->section_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</body>
</html>