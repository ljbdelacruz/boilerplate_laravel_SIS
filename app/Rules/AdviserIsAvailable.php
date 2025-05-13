<?php

namespace App\Rules;

use App\Models\Section;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AdviserIsAvailable implements ValidationRule
{
    protected $schoolYearId;
    protected $currentSectionId;

    /**
     * Create a new rule instance.
     */
    public function __construct($schoolYearId, $currentSectionId = null)
    {
        $this->schoolYearId = $schoolYearId;
        $this->currentSectionId = $currentSectionId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || empty($this->schoolYearId)) {
            return;
        }

        $query = Section::where('adviser_id', $value)
                        ->where('school_year_id', $this->schoolYearId)
                        ->where('is_active', true); // Only check against active sections

        // Exclude the current section if it's being updated
        if ($this->currentSectionId) {
            $query->where('id', '!=', $this->currentSectionId);
        }

        $existingSection = $query->first();

        if ($existingSection) {
            $fail("The selected adviser is already advising another active section ('{$existingSection->name}') for this school year.");
        }
    }
}