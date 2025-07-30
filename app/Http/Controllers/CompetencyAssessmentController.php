<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompetencyAssessment;

class CompetencyAssessmentController extends Controller
{
    /**
     * Update Proposed Grade & Priority.
     */
    public function updateDetails(Request $request, CompetencyAssessment $assessment)
    {
        $validated = $request->validate([
            'proposed_grade' => 'nullable|string|max:10',
            'priority_for_development' => 'required|string|in:Yes,No',
        ]);

        $assessment->update($validated);

        return redirect()->back()->with('success', 'Assessment details have been updated!');
    }
}