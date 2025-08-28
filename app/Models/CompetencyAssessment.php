<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyAssessment extends Model
{
    use HasFactory;

    protected $table = 'competency_assessments';

    protected $fillable = [
        'employee_id', 
        'assessment_date',
        'matrix_grade',
        'period',
        'synergized_team_score',
        'integrity_score',
        'growth_score',
        'adaptive_score',
        'passion_score',
        'manage_planning_score',
        'decision_making_score',
        'relationship_building_score',
        'developing_others_score',
        'proposed_grade',
        'priority_for_development'
    ];
    
 public static function getCompetencyMap(): array
    {
        return [
            'Synergized Team' => 'synergized_team',
            'Integrity for All Action' => 'integrity',
            'Growth for Co-Prosperity' => 'growth',
            'Adaptive to Change' => 'adaptive',
            'Passion for Excellence' => 'passion',
            'Manage and Planning' => 'manage_planning',
            'Decision Making' => 'decision_making',
            'Relationship Building' => 'relationship_building',
            'Developing Others' => 'developing_others'
        ];
    }

    public function employee()
    {
        return $this->setConnection('kpncorp')->belongsTo(Employees::class, 'employee_id', 'employee_id');
    }
}