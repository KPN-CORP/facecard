<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle; 

class ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    protected $employees;
    protected $reportType;
    protected $selectedYear;

    public function __construct($employees, string $reportType, $selectedYear)
    {
        $this->employees = $employees;
        $this->reportType = $reportType;
        $this->selectedYear = $selectedYear;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function headings(): array
    {
        $baseHeadings = [
            'Employee Name', 'Employee ID', 'Business Unit',
            'Job Level', 'Designation',
        ];

        if ($this->reportType === 'talent_report') {
            return array_merge($baseHeadings, ['Talent Status', 'Talent Box']);
        }

        if ($this->reportType === 'idp_progress') {
            return array_merge($baseHeadings, ['IDP Progress']);
        }
        
        return $baseHeadings;
    }

    public function map($employee): array
    {
        $baseData = [
            $employee->fullname,
            $employee->employee_id,
            $employee->group_company,
            $employee->job_level,
            $employee->designation_name,
        ];

        if ($this->reportType === 'talent_report') {
            return array_merge($baseData, [
                $employee->talent_status_for_year,
                $employee->talent_box_for_year
            ]);
        }

        if ($this->reportType === 'idp_progress') {
            return array_merge($baseData, [
                $employee->idp_progress
            ]);
        }

        return $baseData;
    }

    public function title(): string
    {
        $yearString = $this->selectedYear ?: 'All Years';
        return 'Report Data ' . $yearString;
    }
}