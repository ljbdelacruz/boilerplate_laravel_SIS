<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
{
    $sections = Section::with('schoolYear') // Fetch related school year
        ->orderBy('grade_level') // Order by grade level
        ->orderBy('name') // Order by section name
        ->paginate(10); // Use pagination

    return view('sections.index', compact('sections'));
}

    public function create()
    {
        $schoolYears = SchoolYear::where('is_active', true)->get();
        $gradeLevels = \App\Models\GradeLevel::orderBy('grade_level')->get();
        return view('sections.create', compact('schoolYears', 'gradeLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required_if:role,student|string|exists:grade_levels,grade_level',
            'school_year_id' => 'required|exists:school_years,id'
        ]);

        Section::create($validated);

        return redirect()->route('sections.index')
            ->with('success', 'Section created successfully');
    }
    public function edit(Section $section)
    {
        $schoolYears = SchoolYear::where('is_active', true)->get();
        return view('sections.edit', compact('section', 'schoolYears'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|between:7,12',
            'school_year_id' => 'required|exists:school_years,id'
        ]);

        $section->update($validated);

        return redirect()->route('sections.index')
            ->with('success', 'Section updated successfully');
    }

    public function archive(Section $section)
    {
        $section->update(['is_active' => false]);
        return redirect()->route('sections.index')
            ->with('success', 'Section archived successfully');
    }

}