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

class ProposedGradeImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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
        if (empty($row['employee_id'])) {
            return null;
        }

        $model = CompetencyAssessment::updateOrCreate(
            [
                'employee_id' => $row['employee_id'],
                'period'      => now()->year, 
            ],
            [
                'proposed_grade' => $row['proposed_grade']
            ]
        );

        if ($model) {
            $this->successCount++;
        }

        return $model;
    }


    public function rules(): array
    {
        return [
            '*.employee_id' => 'required',
            '*.proposed_grade' => ['required', 'string', Rule::in($this->existingJobLevels)],
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