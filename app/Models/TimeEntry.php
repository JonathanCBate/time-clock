<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use HasFactory;

    // Specify the table associated with the model (optional if it follows Laravel's naming convention)
    protected $table = 'time_entries';

    // Define the attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'duration',
        'description',
    ];
    public function showTimeEntries()
    {
        // Retrieve all time entries from the database
        $timeEntries = TimeEntry::all();
    
        // Pass the data to the Blade view
        return view('stopwatch.index', compact('timeEntries'));
    }
    // Define any relationships, accessors, or mutators as needed
}
