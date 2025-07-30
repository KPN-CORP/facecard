<?php
// app/Imports/ProposedGradeImport.php

namespace App\Imports;

use App\Models\ResultSummary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class ProposedGradeImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public int $successCount = 0;
    protected array $failures = [];

    public function model(array $row)
    {
        if (empty($row['employee_id'])) {
            return null;
        }

        $model = ResultSummary::updateOrCreate(
            ['employee_id' => $row['employee_id']],
            ['proposed_grade' => $row['proposed_grade']]
        );

        if ($model) {
            $this->successCount++;
        }

        return $model;
    }

    public function rules(): array
    {
        return [
            '*.employee_id' => 'required|string|exists:employees,employee_id',
            '*.proposed_grade' => 'required|string',
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