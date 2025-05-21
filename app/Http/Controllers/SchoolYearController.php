<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;


class SchoolYearController extends Controller
{
    use ActivityLogger;
    public function index()
    {
        // Find the current active, non-archived school year
        $activeSchoolYear = SchoolYear::where('is_active', true)
            ->where('is_archived', false)
            ->first();

        $displayableSchoolYears = collect();

        if ($activeSchoolYear) {
            // Add the active school year to our list
            $displayableSchoolYears->push($activeSchoolYear);

            // Find future, non-archived school years
            $futureSchoolYears = SchoolYear::where('is_archived', false)
                ->where('start_year', '>', $activeSchoolYear->start_year)
                ->orderBy('start_year', 'asc') 
                ->get();

            
            $displayableSchoolYears = $displayableSchoolYears->merge($futureSchoolYears);
        }
        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentItems = $displayableSchoolYears->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $schoolYears = new LengthAwarePaginator($currentItems, $displayableSchoolYears->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        // Count the number of active school years
        $activeSchoolYearCount = SchoolYear::where('is_archived', false)
            ->where('is_active', true)->count();
        return view('school-years.index', compact('schoolYears', 'activeSchoolYearCount'));
    }

    public function create()
    {
        $latestSchoolYear = SchoolYear::where('is_archived', false)
                                      ->orderBy('end_year', 'desc')
                                      ->first();

        $suggestedStartYear = date('Y');
        $suggestedEndYear = date('Y') + 1;

        if ($latestSchoolYear) {
            $suggestedStartYear = $latestSchoolYear->end_year;
            $suggestedEndYear = $latestSchoolYear->end_year + 1;
        }
        return view('school-years.create', compact('suggestedStartYear', 'suggestedEndYear'));
    }

    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'start_year' => 'required|integer|digits:4',
            'end_year' => [
                'required',
                'integer',
                'digits:4',
                'gt:start_year',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value - $request->start_year !== 1) {
                        $fail('The school year range must be exactly one year.');
                    }
                }
            ]
        ]);

        // Check for conflicting school years
        $overlap = SchoolYear::where('is_archived', false)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_year', [$validated['start_year'], $validated['end_year'] - 1])
                    ->orWhereBetween('end_year', [$validated['start_year'] + 1, $validated['end_year']])
                    ->orWhere(function ($query) use ($validated) {
                        $query->where('start_year', '<', $validated['start_year'])
                            ->where('end_year', '>', $validated['end_year']);
                    });
            })->exists();

        if ($overlap) {
            $this->logActivity(
                'create',
                'Warning: Failed to create school year - overlap detected',
                'school_year',
                null,
                $validated,
                'error'
            );
            
            return redirect()->back()
                ->withErrors(['error' => 'A school year with overlapping dates already exists.'])
                ->withInput();
        }

        if ($request->has('is_active') && $request->is_active) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        $schoolYear = SchoolYear::create($validated);

        $this->logActivity(
            'create',
            'Created new school year: ' . $schoolYear->start_year . '-' . $schoolYear->end_year,
            'school_year',
            null,
            $schoolYear->toArray(),
            'success'
        );

        return redirect()->route('school-years.index')->with('success', 'School Year created successfully');
    } catch (\Exception $e) {
        $this->logActivity(
            'create',
            'Warning: Failed to create school year: ' . $e->getMessage(),
            'school_year',
            null,
            $validated ?? null,
            'error'
        );
        throw $e;
    }
}

    public function edit(SchoolYear $schoolYear)
    {
        return view('school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear)
    {
        try {
            $oldData = $schoolYear->toArray();
            $validated = $request->validate([
                'start_year' => 'required|integer|digits:4',
                'end_year' => [
                    'required',
                    'integer',
                    'digits:4',
                    'gt:start_year',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value - $request->start_year !== 1) {
                            $fail('The school year range must be exactly one year.');
                        }
                    }
                ]
            ]);
    
            $overlap = SchoolYear::where('id', '!=', $schoolYear->id)
                ->where('is_archived', false)
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('start_year', [$validated['start_year'], $validated['end_year'] - 1])
                        ->orWhereBetween('end_year', [$validated['start_year'] + 1, $validated['end_year']])
                        ->orWhere(function ($query) use ($validated) {
                            $query->where('start_year', '<', $validated['start_year'])
                                ->where('end_year', '>', $validated['end_year']);
                        });
                })->exists();
    
            if ($overlap) {
                $this->logActivity(
                    'update',
                    'Warning: Failed to update school year - overlap detected',
                    'school_year',
                    $oldData,
                    $validated,
                    'error'
                );
                
                return redirect()->back()
                    ->withErrors(['error' => 'A school year with overlapping dates already exists.'])
                    ->withInput();
            }
    
            $schoolYear->update($validated);
    
            $this->logActivity(
                'update',
                'Updated school year: ' . $schoolYear->start_year . '-' . $schoolYear->end_year,
                'school_year',
                $oldData,
                $schoolYear->fresh()->toArray(),
                'success'
            );
    
            return redirect()->route('school-years.index')
                ->with('success', 'School Year updated successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'update',
                'Warning: Failed to update school year: ' . $e->getMessage(),
                'school_year',
                $oldData ?? null,
                $validated ?? null,
                'error'
            );
            throw $e;
        }
    }

    public function destroy(SchoolYear $schoolYear)
    {
        try {
            $oldData = $schoolYear->toArray();
            $schoolYear->update(['is_archived' => true]);
    
            $this->logActivity(
                'archive',
                'Archived school year: ' . $schoolYear->start_year . '-' . $schoolYear->end_year,
                'school_year',
                $oldData,
                $schoolYear->fresh()->toArray(),
                'success'
            );
    
            return redirect()->route('school-years.index')
                ->with('success', 'School Year archived successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'archive',
                'Warning: Failed to archive school year: ' . $e->getMessage(),
                'school_year',
                $oldData ?? null,
                null,
                'error'
            );
            throw $e;
        }
    }
    public function toggleActive(SchoolYear $schoolYear)
    {
        // Prevent deactivating the last active school year
        if ($schoolYear->is_active) {
            $activeCount = SchoolYear::where('is_archived', false)->where('is_active', true)->count();
            if ($activeCount <= 1) {
                return redirect()->route('school-years.index')->with('error', 'Cannot deactivate the only active school year.');
            }
        }
        // If we're activating this school year, deactivate all others first
        try {
            $oldData = $schoolYear->toArray();
            
            if (!$schoolYear->is_active) {
                SchoolYear::where('is_active', true)->update(['is_active' => false]);
            }
            
            $schoolYear->update(['is_active' => !$schoolYear->is_active]);
    
            $this->logActivity(
                'toggle',
                'Changed school year status to: ' . ($schoolYear->is_active ? 'active' : 'inactive'),
                'school_year',
                $oldData,
                $schoolYear->fresh()->toArray(),
                'success'
            );
            
            return redirect()->route('school-years.index')
                ->with('success', 'School year status updated successfully');
        } catch (\Exception $e) {
            $this->logActivity(
                'toggle',
                'Warning: Failed to toggle school year status: ' . $e->getMessage(),
                'school_year',
                $oldData ?? null,
                null,
                'error'
            );
            throw $e;
        }
    }
}