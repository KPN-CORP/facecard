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
        
        $parseDate = function($dateValue) {
            if (empty($dateValue) || $dateValue === '-') {
                return null;
            }
            if (is_numeric($dateValue)) {
                return Date::excelToDateTimeObject($dateValue);
            }
            try {
                return Carbon::createFromFormat('d-m-Y', $dateValue);
            } catch (\Exception $e) {
                return null;
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

    public function rules(): array
    {
        return [
            '*.employee_id'       => 'required',
            '*.development_model' => 'required|string',
            '*.competency_type'   => 'required|string',
            '*.competency_name'   => 'required|string',
            // --- ATURAN VALIDASI TANGGAL DIPERBARUI ---
            '*.time_frame_start'  => ['required', 'date_format:d-m-Y', 'before_or_equal:today'],
            '*.time_frame_end'    => ['required', 'date_format:d-m-Y', 'before_or_equal:today'],
            '*.realization_date'  => [
                'nullable', 
                'date_format:d-m-Y',
                'before_or_equal:today',
            ],
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            '*.employee_id.exists' => 'Employee ID :input not found',
            '*.date_format' => 'The :attribute must match the format DD-MM-YYYY.',
            '*.before_or_equal' => 'The :attribute must be a date before or equal to today.',
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