<?php

namespace App\Imports;

use App\Models\MatrixGradeConfig;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class DataMasterImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public int $successCount = 0; 
    protected array $failures = [];

    public function model(array $row)
    {
        $assessment = MatrixGradeConfig::updateOrCreate(
            ['period' => $row['period'], 'grade_level' => $row['grade_level']],
            [
                'synergized_team_min' => $row['synergized_team_min'],
                'integrity_min' => $row['integrity_min'],
                'growth_min' => $row['growth_min'],
                'adaptive_min' => $row['adaptive_min'],
                'passion_min' => $row['passion_min'],
                'manage_planning_min' => $row['manage_planning_min'],
                'decision_making_min' => $row['decision_making_min'],
                'relationship_building_min' => $row['relationship_building_min'],
                'developing_others_min' => $row['developing_others_min'],
                'overall_status_min' => $row['overall_status_min'],
            ]
        );

        if ($assessment) {
            $this->successCount++;
        }

        return $assessment;
    }

    /**
     * Menambahkan aturan validasi yang diperlukan.
     */
    public function rules(): array
    {
        return [
            '*.period' => 'required|numeric|digits:4',
            '*.grade_level' => 'required|string',
            '*.synergized_team_min' => 'required|numeric',
            '*.integrity_min' => 'required|numeric',
            '*.growth_min' => 'required|numeric',
            '*.adaptive_min' => 'required|numeric',
            '*.passion_min' => 'required|numeric',
            '*.manage_planning_min' => 'required|numeric',
            '*.decision_making_min' => 'required|numeric',
            '*.relationship_building_min' => 'required|numeric',
            '*.developing_others_min' => 'required|numeric',
            '*.overall_status_min' => 'required|string',
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