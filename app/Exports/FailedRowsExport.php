<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FailedRowsExport implements FromCollection, WithHeadings
{
    protected $failures;

    public function __construct(array $failures)
    {
        $this->failures = $failures;
    }

    public function collection()
    {
        return collect($this->failures);
    }

    public function headings(): array
    {
        // Header for error file
        return [
            'Row',
            'Attribute',
            'Error Message',
            'Value Provided'
        ];
    }
}