<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::where('is_archived', false)->get();
        return view('school-years.index', compact('schoolYears'));
    }

    public function create()
    {
        return view('school-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
        ]);

        SchoolYear::create($validated);

        return redirect()->route('school-years.index')
            ->with('success', 'School Year created successfully');
    }

    public function edit(SchoolYear $schoolYear)
    {
        return view('school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
        ]);

        $schoolYear->update($validated);

        return redirect()->route('school-years.index')
            ->with('success', 'School Year updated successfully');
    }

    public function destroy(SchoolYear $schoolYear)
    {
        $schoolYear->update(['is_archived' => true]);
        return redirect()->route('school-years.index')
            ->with('success', 'School Year archived successfully');
    }
}