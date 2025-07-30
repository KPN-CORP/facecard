<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TrainingCertification extends Model
{
    use HasFactory;
    protected $table = 'trainings_certifications'; 
    protected $guarded = ['id'];
    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id', 'employee_id');
    }
}