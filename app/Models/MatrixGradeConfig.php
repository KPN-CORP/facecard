<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class MatrixGradeConfig extends Model
{
    use HasFactory;
    protected $table = 'matrix_grades_configs';
    protected $guarded = ['id']; 
}