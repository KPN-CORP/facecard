<?php

namespace App\Imports;

use App\Models\CompetencyAssessment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CompetencyAssessmentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public int $successCount = 0;
    protected array $failures = [];

    public function model(array $row)
    {
        $assessmentDate = \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['assessment_date']));
        
        $assessment = CompetencyAssessment::updateOrCreate(
            [
                'employee_id'  => $row['employee_id'],
                'period'       => $assessmentDate->year,
            ],
            [
                'matrix_grade' => $row['matrix_grade'],
                'synergized_team_score' => $row['synergized_team_score'],
                'integrity_score'       => $row['integrity_score'],
                'growth_score'          => $row['growth_score'],
                'adaptive_score'        => $row['adaptive_score'],
                'passion_score'         => $row['passion_score'],
                'manage_planning_score' => $row['manage_planning_score'],
                'decision_making_score' => $row['decision_making_score'],
                'relationship_building_score' => $row['relationship_building_score'],
                'developing_others_score' => $row['developing_others_score'],
                'assessment_date' => $assessmentDate,
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
            '*.employee_id' => 'required|string|exists:employees,employee_id',
            '*.assessment_date' => 'required',
            '*.matrix_grade' => 'required|string',
            
            '*.synergized_team_score'       => 'required|numeric|min:0',
            '*.integrity_score'             => 'required|numeric|min:0',
            '*.growth_score'                => 'required|numeric|min:0',
            '*.adaptive_score'              => 'required|numeric|min:0',
            '*.passion_score'               => 'required|numeric|min:0',
            '*.manage_planning_score'       => 'required|numeric|min:0',
            '*.decision_making_score'       => 'required|numeric|min:0',
            '*.relationship_building_score' => 'required|numeric|min:0',
            '*.developing_others_score'     => 'required|numeric|min:0',
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