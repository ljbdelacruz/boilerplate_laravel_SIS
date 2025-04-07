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
            'start_year' => 'required|integer|digits:4',
            'end_year' => 'required|integer|digits:4|gt:start_year',
        ]);

        // Deactivate all other school years if this one is set as active
        if ($request->has('is_active') && $request->is_active) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        SchoolYear::create($validated);
        return redirect()->route('school-years.index')->with('success', 'School Year created successfully');
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

    public function toggleActive(SchoolYear $schoolYear)
    {
        // If we're activating this school year, deactivate all others first
        if (!$schoolYear->is_active) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }
        
        $schoolYear->update(['is_active' => !$schoolYear->is_active]);
        
        return redirect()->route('school-years.index')
            ->with('success', 'School year status updated successfully');
    }
}