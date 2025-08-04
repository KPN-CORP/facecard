<?php

namespace App\Imports;

use App\Models\InternalMovement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class InternalMovementImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public int $successCount = 0;
    protected array $failures = [];

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Abaikan baris jika tidak ada employee_id
        if (empty($row['employee_id'])) {
            return null;
        }
        
        // Tambah hitungan sukses
        $this->successCount++;

        return new InternalMovement([
            'employee_id'     => $row['employee_id'],
            'from_date'       => Date::excelToDateTimeObject($row['from_date']),
            'to_date'         => !empty($row['to_date']) ? Date::excelToDateTimeObject($row['to_date']) : null,
            'is_promotion'    => $row['is_promotion'],
            'job_level'       => $row['job_level'],
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.employee_id'  => 'required|string|exists:employees,employee_id',
            '*.from_date'    => 'required|numeric', // Excel mengirim tanggal sebagai angka
            '*.to_date'      => 'nullable|numeric',
            '*.is_promotion' => 'required|string',
            '*.job_level'    => 'required|string',
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