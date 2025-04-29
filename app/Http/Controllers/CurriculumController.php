<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\Course;

class CurriculumController extends Controller
{
    public function index()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (!$activeSchoolYear) {
            return view('curriculums.index', [
                'sections' => collect(),
                'activeSchoolYear' => null,
            ]);
        }

        $sections = Section::where('school_year_id', $activeSchoolYear->id)
            ->with(['students', 'curriculums.subject'])
            ->get();

        return view('curriculums.index', compact('sections', 'activeSchoolYear'));
    }

    public function create()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $sections = $activeSchoolYear
            ? Section::where('school_year_id', $activeSchoolYear->id)->get()
            : collect();
        $courses = Course::all();

        return view('curriculums.create', compact('sections', 'courses', 'activeSchoolYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:courses,id',
            'time' => 'required|string|max:255',
        ]);

        Curriculum::create($validated);

        return redirect()->route('curriculums.index')->with('success', 'Curriculum added successfully!');
    }

    public function edit(Curriculum $curriculum)
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $sections = $activeSchoolYear
            ? Section::where('school_year_id', $activeSchoolYear->id)->get()
            : collect();
        $courses = Course::all();

        return view('curriculums.edit', compact('curriculum', 'sections', 'courses', 'activeSchoolYear'));
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:courses,id',
            'time' => 'required|string|max:255',
        ]);

        $curriculum->update($validated);

        return redirect()->route('curriculums.index')->with('success', 'Curriculum updated successfully!');
    }

    public function destroy(Curriculum $curriculum)
    {
        $curriculum->delete();

        return redirect()->route('curriculums.index')->with('success', 'Curriculum deleted successfully!');
    }
}
