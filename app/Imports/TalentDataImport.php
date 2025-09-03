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
        if (empty($row['employee_id']) || empty($row['period'])) {
            return null;
        }

        $paExists = PerformanceAppraisal::where('employee_id', $row['employee_id'])
                                        ->where('appraisal_year', $row['period'])
                                        ->exists();

        // If PA doesn't exist, failure will be shown 
        if (!$paExists) {
            $this->onFailure(new Failure(
                $this->successCount + count($this->failures) + 2, 
                'period', 
                ['No Performance Appraisal found for this employee in the year ' . $row['period'] . '.'], 
                $row 
            ));
            return null; 
        }

        $data = [];
        if ($this->importType === 'talent_box') {
            $data['talent_box'] = $row['talent_box'];
        } elseif ($this->importType === 'potential') {
            $data['potential'] = $row['potential'];
        }

        if (empty($data)) {
            return null;
        }
        
        $appraisal = PerformanceAppraisal::updateOrCreate(
            ['employee_id' => $row['employee_id'], 'appraisal_year' => $row['period']],
            $data
        );

        if ($appraisal) {
            $this->successCount++;
        }

        return $appraisal;
    }

    // Biarkan validasi dasar di sini
    public function rules(): array
    {
        $rules = [
            '*.employee_id' => 'required|exists:users,employee_id|size:11',
            '*.period'      => 'required|numeric|digits:4',
        ];

        if ($this->importType === 'talent_box') {
            $rules['*.talent_box'] = 'required|string';
        } elseif ($this->importType === 'potential') {
            $rules['*.potential'] = 'required|string';
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