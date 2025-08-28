<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompetencyAssessment; 
use Illuminate\Validation\ValidationException;

class CompetencyAssessmentController extends Controller
{
    /**
     * Store atau Update data assessment.
     */
    public function storeOrUpdate(Request $request)
    {
        // Validasi semua input dari modal
        $validatedData = $request->validate([
            'employee_id' => 'required|string',
            'assessment_date' => [
    'required',
    'date',
    function ($attribute, $value, $fail) {
        if (date('Y', strtotime($value)) > date('Y')) {
            $fail('The assessment year cannot be in the future.');
        }
    },
],
            'matrix_grade' => 'required|string',
            'proposed_grade' => 'nullable|string',
            'priority_for_development' => 'nullable|string',
            'synergized_team_score' => 'required|integer|min:0|max:4',
            'integrity_score' => 'required|integer|min:0|max:4',
            'growth_score' => 'required|integer|min:0|max:4',
            'adaptive_score' => 'required|integer|min:0|max:4',
            'passion_score' => 'required|integer|min:0|max:4',
            'manage_planning_score' => 'required|integer|min:0|max:4',
            'decision_making_score' => 'required|integer|min:0|max:4',
            'relationship_building_score' => 'required|integer|min:0|max:4',
            'developing_others_score' => 'required|integer|min:0|max:4',
        ]);

        // Logic Update-or-Create: Cari berdasarkan employee dan tahun asesmen
        CompetencyAssessment::updateOrCreate(
    [
        'employee_id' => $validatedData['employee_id'],
        'period' => date('Y', strtotime($validatedData['assessment_date'])) 
    ],
    $validatedData
);

        return redirect()->route('employee.profile', ['employeeId' => $validatedData['employee_id']])
                         ->with('success', 'Competency assessment has been saved successfully!');
    }
}