<?php

namespace App\Imports;

use App\Models\IndividualDevelopmentPlan;
use App\Models\DevelopmentModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class DevelopmentPlanImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    protected array $failures = [];
    private $developmentModelsMap;
    public int $successCount = 0;

    public function __construct()
    {
        $this->developmentModelsMap = DevelopmentModel::all()->pluck('id', 'percentage');
    }

    public function model(array $row)
    {
        if (empty($row['employee_id'])) {
            return null;
        }

        $modelId = null;
        if (isset($row['development_model']) && is_numeric($row['development_model'])) {
            $percentageValue = $row['development_model'];
            if ($percentageValue > 0 && $percentageValue <= 1) {
                $percentageValue = $percentageValue * 100;
            }
            $lookupKey = round($percentageValue);
            $modelId = $this->developmentModelsMap->get($lookupKey);
        }
        if (is_null($modelId)) {
        return null; 
    }

        // Function to make date have to fill by date format or '-'
        $parseDate = function($dateValue) {
            if (empty($dateValue) || $dateValue === '-') {
                return null;
            }
            if (is_numeric($dateValue)) {
                return Date::excelToDateTimeObject($dateValue);
            }
            try {
                return Carbon::parse($dateValue);
            } catch (\Exception $e) {
                return null; // Will be failed if the formal validation wrong
            }
        };

        $this->successCount++;

        return new IndividualDevelopmentPlan([
            'employee_id'          => $row['employee_id'],
            'development_model_id' => $modelId,
            'competency_type'      => $row['competency_type'], 
            'competency_name'      => $row['competency_name'],
            'review_tools'         => $row['review_tools'],
            'development_program'  => $row['development_program'],
            'expected_outcome'     => $row['expected_outcome'],
            'time_frame_start'     => $parseDate($row['time_frame_start']),
            'time_frame_end'       => $parseDate($row['time_frame_end']),
            'realization_date'     => $parseDate($row['realization_date']),
            'result_evidence'      => $row['result_evidence'],
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.employee_id' => 'required|exists:employees,employee_id',
            '*.development_model' => 'required|numeric',
            '*.competency_type'   => 'required|string',
            '*.competency_name'   => 'required|string',
            '*.time_frame_start'  => 'required|numeric',
            '*.time_frame_end'    => 'required|numeric',
            '*.realization_date'  => [
                'required', // Mandatory to fill by date or '-'
                function ($attribute, $value, $fail) {

                    if ($value === '-') {
                        return;
                    }
                    if (is_numeric($value)) {
                        return;
                    }
                    $fail('The '.$attribute.' must be a valid date or "-". Empty is not allowed.');
                },
            ],
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