<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MovementTransaction extends Model {
    protected $connection = 'kpncorp';

    protected $guarded = ['id'];
}