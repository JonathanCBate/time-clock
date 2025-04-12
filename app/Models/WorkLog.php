<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// In WorkLog model (app/Models/WorkLog.php)
class WorkLog extends Model
{
    protected $fillable = [
        'user_id',
        'work_description',
        'elapsed_time',
        'start_time',
        'end_time',
    ];
    

    protected $table = 'work_logs';
}
