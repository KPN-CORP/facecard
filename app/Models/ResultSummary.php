<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ResultSummary extends Model
{
    use HasFactory;
    protected $table = 'result_summaries'; 
    protected $guarded = ['id'];
    public function employee()
    {
        return $this->setConnection('kpncorp')->belongsTo(Employees::class, 'employee_id', 'employee_id');
    }
}