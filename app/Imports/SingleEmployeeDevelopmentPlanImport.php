<?php

namespace App\Imports;

use App\Models\IndividualDevelopmentPlan;
use App\Models\DevelopmentModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon; // Pastikan Carbon di-import

class SingleEmployeeDevelopmentPlanImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected string $employee_id;
    private $developmentModelsMap;
    public $failures = [];
    public int $successCount = 0;

    public function __construct(string $employee_id)
    {
        $this->employee_id = $employee_id;
        $this->developmentModelsMap = DevelopmentModel::all()->pluck('id', 'percentage');
    }

    public function model(array $row)
    {
        if (empty($row['competency_type']) || empty($row['competency_name'])) {
            return null;
        }
        
        $modelId = null; 
        if (isset($row['development_model']) && is_numeric($row['development_model'])) {
            $percentageValue = $row['development_model'];
            if ($percentageValue > 0 && $percentageValue <= 1) {
                $percentageValue = $percentageValue * 100;
            }
            $lookupKey = round($percentageValue);
            $modelId = $this->developmentModelsMap->get($lookupKey, null);
        }

        // Function to parse date
        $parseDate = function($dateValue) {
            if (empty($dateValue) || trim($dateValue) === '-') {
                return null; 
            }
            if (is_numeric($dateValue)) {
                return Date::excelToDateTimeObject($dateValue);
            }
            try {
                return Carbon::parse($dateValue);
            } catch (\Exception $e) {
                return 'invalid_date'; 
            }
        };

        $this->successCount++;

        return new IndividualDevelopmentPlan([
            'employee_id'          => $this->employee_id, 
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

    public function rules(): array
    {
        // validate rules
        return [
            '*.development_model' => 'nullable', 
            '*.competency_type'   => 'required|string',
            '*.competency_name'   => 'required|string',
            '*.time_frame_start'  => 'required|numeric',
            '*.time_frame_end'    => 'required|numeric',
            '*.realization_date'  => [ 
                'nullable',
                function ($attribute, $value, $fail) {
                    if (empty($value)) { return; } 
                    if ($value === '-') { return; }
                    if (is_numeric($value)) { return; } 
                    $fail('The '.$attribute.' must be a valid date, "-", or empty.');
                },
            ],
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