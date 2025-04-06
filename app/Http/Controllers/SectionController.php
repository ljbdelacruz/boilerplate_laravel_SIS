<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['schoolYear'])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();
            
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::where('is_active', true)->get();
        return view('admin.sections.create', compact('schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|between:7,12',
            'school_year_id' => 'required|exists:school_years,id'
        ]);

        Section::create($validated);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section created successfully');
    }

    public function archive(Section $section)
    {
        $section->update(['is_active' => false]);
        return redirect()->route('admin.sections.index')
            ->with('success', 'Section archived successfully');
    }
}