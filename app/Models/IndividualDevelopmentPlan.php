<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualDevelopmentPlan extends Model
{
    use HasFactory;

    protected $table = 'individual_development_plans';
    
    protected $fillable = [
        'employee_id',
        'development_model_id',
        'competency_type',
        'competency_name',
        'review_tools',
        'development_program',
        'expected_outcome',
        'time_frame_start',
        'time_frame_end',
        'realization_date',
        'result_evidence',
    ];

    public function developmentModel()
    {
        return $this->belongsTo(DevelopmentModel::class, 'development_model_id');
    }
}
