<?php

namespace App\Imports;

use App\Models\PerformanceAppraisal; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class TalentDataImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected array $failures = [];
    protected string $importType;
    public int $successCount = 0;

    public function __construct(string $importType)
    {
        $this->importType = $importType;
    }

    public function model(array $row)
    {
        // Ignore row if essential data is missing
        if (empty($row['employee_id']) || empty($row['period'])) {
            return null;
        }

        $data = [];
        // Map the correct data based on the import type
        if ($this->importType === 'talent_box') {
            $data['talent_box'] = $row['talent_box'];
        } elseif ($this->importType === 'talent_status') {
            $data['talent_status'] = $row['talent_status'];
        }

        if (empty($data)) {
            return null;
        }
        
        // Use updateOrCreate on the PerformanceAppraisal model
        // It will find a record by employee_id AND year, or create a new one.
        $appraisal = PerformanceAppraisal::updateOrCreate(
            [
                'employee_id'    => $row['employee_id'],
                'appraisal_year' => $row['period'],
            ],
            $data
        );

        if ($appraisal) {
            $this->successCount++;
        }

        return $appraisal;
    }

    public function rules(): array
    {
        // Add validation for the new 'period' column
        $rules = [
            '*.employee_id' => 'required|exists:employees,employee_id',
            '*.period'      => 'required|numeric|digits:4',
        ];

        // Add specific rules for each import type
        if ($this->importType === 'talent_box') {
            $rules['*.talent_box'] = 'required|string';
        } elseif ($this->importType === 'talent_status') {
            $rules['*.talent_status'] = 'required|string';
        }

        return $rules;
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