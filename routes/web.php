<?php

use App\Http\Controllers\ClockController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-mail', function () {
    $pdfPath = storage_path('app/public/work_time_report.pdf');
    Mail::to('jonathanbate09@gmail.com')->send(new \App\Mail\sendCSVEmail($pdfPath));
});


Route::post('/send-pdf-email', [ClockController::class, 'sendCSVEmail'])->name('send_pdf_email');

Route::get('/generate-pdf', [ClockController::class, 'generateCSV'])->name('generate_pdf');
Route::get('/calendar', [ClockController::class, 'calendar'])->name('calendar');
// Work Clock Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ClockController::class, 'index'])->name('dashboard'); 
    Route::post('/dashboard', [ClockController::class, 'post'])->name('post_data');
});
Route::post('/update_work_log/{id}', [ClockController::class, 'update'])->name('update_work_log');
Route::delete('/work-log/{id}', [ClockController::class, 'destroy'])->name('work-log.destroy');
Route::post('/work-log/bulk-delete', [ClockController::class, 'bulkDestroy'])->name('work-log.bulk-delete');
Route::get('/weekly-total', [ClockController::class, 'getWeeklyTotal'])->name('get_weekly_total');

Route::delete('/work-logs/{id}', [ClockController::class, 'destroy'])->name('work-logs.destroy');


// Work Clock Page (Optional if not needed)
Route::get('/work-clock', [ClockController::class, 'index']);

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
