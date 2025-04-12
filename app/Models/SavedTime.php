<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedTime extends Model
{
  // In the migration file
  public function up()
  {
      Schema::create('saved_times', function (Blueprint $table) {
          $table->id();
          $table->string('time');
          $table->timestamps();
      });
  }
  public function saveTime(Request $request)
  {
      $validated = $request->validate([
          'time' => 'required|string|max:255',
      ]);
  
      SavedTime::create(['time' => $validated['time']]);
      return response()->json(['success' => true]);
  }
    
// In App/Models/SavedTime.php
protected $fillable = ['time'];

}
