<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;
    protected $table = 'import_logs';
     protected $fillable = [
        'data_type', 
        'import_date', 
        'result', 
        'status',
        'original_file_path',
        'error_file_path',
        'user_id' 
    ];
}