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

class IdpSheetImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    protected array $failures = [];
    private $developmentModelsMap;
    public int $successCount = 0;

    public function __construct()
    {
        $this->developmentModelsMap = DevelopmentModel::all()->pluck('id', 'percentage');
    }

    /**
     * Prepare the data for validation.
     * This method runs BEFORE rules().
     */
    public function prepareForValidation($data, $index)
    {
        // Define the parsing logic here
        $parseDate = function($dateValue) {
            if (empty($dateValue) || $dateValue === '-') {
                return null;
            }
            if (is_numeric($dateValue)) {
                return Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
            }
            try {
                // Carbon::parse is flexible and handles d-m-Y, Y-m-d, etc.
                return Carbon::parse($dateValue)->format('Y-m-d');
            } catch (\Exception $e) {
                return $dateValue; // Return original value on failure to let validator catch it
            }
        };

        // Apply parsing to date fields
        $data['time_frame_start'] = $parseDate($data['time_frame_start']);
        $data['time_frame_end'] = $parseDate($data['time_frame_end']);
        $data['realization_date'] = $parseDate($data['realization_date']);

        return $data;
    }

    public function model(array $row)
    {
        $modelId = null;
        if (isset($row['development_model'])) {
            preg_match('/^\d+/', (string) $row['development_model'], $matches);
            if (!empty($matches)) {
                $percentageValue = (int) $matches[0];
                $modelId = $this->developmentModelsMap->get($percentageValue);
            }
        }

        if (empty($row['employee_id']) || is_null($modelId)) {
            return null;
        }

        $this->successCount++;

        // The data is already parsed and validated, so just pass it through
        return new IndividualDevelopmentPlan([
            'employee_id'          => $row['employee_id'],
            'development_model_id' => $modelId,
            'competency_type'      => $row['competency_type'],
            'competency_name'      => $row['competency_name'],
            'review_tools'         => $row['review_tools'],
            'development_program'  => $row['development_program'],
            'expected_outcome'     => $row['expected_outcome'],
            'time_frame_start'     => $row['time_frame_start'],
            'time_frame_end'       => $row['time_frame_end'],
            'realization_date'     => $row['realization_date'],
            'result_evidence'      => $row['result_evidence'],
        ]);
    }

    /**
     * Define the validation rules for each row.
     * These rules now run on the data AFTER it's been prepared.
     */
    public function rules(): array
    {
        return [
            '*.employee_id'       => 'required|exists:kpncorp.employees,employee_id',
            '*.development_model' => 'required|string',
            '*.competency_type'   => 'required|string',
            '*.competency_name'   => 'required|string',
            '*.time_frame_start'  => ['required', 'date', 'before_or_equal:today'],
            '*.time_frame_end'    => ['required', 'date', 'after_or_equal:*.time_frame_start', 'before_or_equal:today'],
            '*.realization_date'  => [
                'nullable',
                'date',
                'after_or_equal:*.time_frame_start',
                'before_or_equal:today',
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.employee_id.exists' => 'Employee ID :input not found.',
            '*.date'               => 'The :attribute field must be a valid date.',
            '*.before_or_equal'    => 'The :attribute must be a date before or equal to today.',
            '*.after_or_equal'     => 'The :attribute must be a date after or on the start date.',
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