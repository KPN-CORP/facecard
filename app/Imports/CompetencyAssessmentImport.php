<?php

namespace App\Imports;

use App\Models\CompetencyAssessment;
use App\Models\Employees;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;

class CompetencyAssessmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public int $successCount = 0;
    protected array $failures = [];
    private $existingJobLevels;

    public function __construct()
    {
        $this->existingJobLevels = Employees::whereNotNull('job_level')->pluck('job_level')->unique()->toArray();
    }

    public function model(array $row)
    {
        $assessmentDate = Carbon::createFromFormat('d-m-Y', $row['assessment_date']);
        
        $assessment = CompetencyAssessment::updateOrCreate(
            [
                'employee_id'  => $row['employee_id'],
                'period'       => $assessmentDate->year,
            ],
            [
                'proposed_grade' => $row['proposed_grade'],
                'assessment_date' => $assessmentDate,
                'synergized_team_score' => $row['synergized_team_score'],
                'integrity_score'       => $row['integrity_score'],
                'growth_score'          => $row['growth_score'],
                'adaptive_score'        => $row['adaptive_score'],
                'passion_score'         => $row['passion_score'],
                'manage_planning_score' => $row['manage_planning_score'],
                'decision_making_score' => $row['decision_making_score'],
                'relationship_building_score' => $row['relationship_building_score'],
                'developing_others_score' => $row['developing_others_score'],
            ]
        );

        if ($assessment) {
            $this->successCount++;
        }

        return $assessment;
    }


    public function rules(): array
    {
        return [
            '*.employee_id' => 'required|exists:users,employee_id|size:11',
            '*.assessment_date' => [
                'required',
                'date_format:d-m-Y', 
                'before_or_equal:today', 
            ],

            '*.proposed_grade' => ['required', 'string', Rule::in($this->existingJobLevels)],
            '*.synergized_team_score'       => 'required|numeric|min:0|max:4',
            '*.integrity_score'             => 'required|numeric|min:0|max:4',
            '*.growth_score'                => 'required|numeric|min:0|max:4',
            '*.adaptive_score'              => 'required|numeric|min:0|max:4',
            '*.passion_score'               => 'required|numeric|min:0|max:4',
            '*.manage_planning_score'       => 'required|numeric|min:0|max:4',
            '*.decision_making_score'       => 'required|numeric|min:0|max:4',
            '*.relationship_building_score' => 'required|numeric|min:0|max:4',
            '*.developing_others_score'     => 'required|numeric|min:0|max:4',
        ];
    }
    public function customValidationMessages()
    {
        return [
            '*.employee_id.exists' => 'Employee ID :input not found',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function failures(): array
    {
        return $this->failures;
    }
}