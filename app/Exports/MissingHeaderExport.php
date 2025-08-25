<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class MissingHeaderExport implements FromArray, WithHeadings
{
    protected $importType;
    protected $errorMessage;

    public function __construct(string $importType, string $errorMessage)
    {
        $this->importType = $importType;
        $this->errorMessage = $errorMessage;
    }

    public function array(): array
    {
        return [
            ['Error' => $this->errorMessage]
        ];
    }

    public function headings(): array
    {
        return $this->getHeadersForType();
    }

    private function getHeadersForType(): array
{
    // first 'error' column used to show error message to user
    $headers = ['Error']; 

    switch ($this->importType) {
        case 'competency_assessment':
            return array_merge($headers, [
                'employee_id', 'assessment_date', 'matrix_grade', 
                'synergized_team_score', 'integrity_score', 'growth_score', 
                'adaptive_score', 'passion_score', 'manage_planning_score', 
                'decision_making_score', 'relationship_building_score', 'developing_others_score'
            ]);
        
        case 'idp':
            return array_merge($headers, [
                'employee_id', 'competency_type', 'development_point', 'review_tools',
                'development_type', 'development_name', 'time_frame_start', 'time_frame_end',
                'realization_date', 'result_evidence'
            ]);

        case 'data_master':
            return array_merge($headers, [
                'period', 'grade_level', 'synergized_team_min', 'integrity_min',
                'growth_min', 'adaptive_min', 'passion_min', 'manage_planning_min',
                'decision_making_min', 'relationship_building_min', 'developing_others_min'
            ]);

        case 'talent_box':
            // Hanya memerlukan employee_id dan talent_box
            return array_merge($headers, ['employee_id', 'talent_box']);
        
        case 'talent_status':
            // Hanya memerlukan employee_id dan talent_status
            return array_merge($headers, ['employee_id', 'talent_status']);

        case 'proposed_grade':
            // Hanya memerlukan employee_id dan proposed_grade
            return array_merge($headers, ['employee_id', 'proposed_grade']);
    }

    return $headers;
}
}