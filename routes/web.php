<?php
use App\Http\Controllers\StopwatchController;
use App\Http\Controllers\ProfileController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;








Route::post('/save-color', [StopwatchController::class, 'saveColor'])->name('save.color');

Route::get('/saved-times', [StopwatchController::class, 'index'])->name('saved-times');
Route::post('/save-time', [StopwatchController::class, 'saveTime']);
Route::post('/clear-saved-times', [StopwatchController::class, 'clearSavedTimes']);
Route::get('/make-pdf', [StopwatchController::class, 'generatePDF'])->name('make-pdf');
Route::post('/remove-image', [StopwatchController::class, 'removeImage'])->name('remove.image');

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/email-pdf', [StopwatchController::class, 'emailPdf'])->name('email.pdf');
Route::get('/clock',[StopwatchController::class, 'showClock'])->name('Clock');
Route::get('/upload', [StopwatchController::class, 'showUploadForm'])->name('file.upload.form');
Route::post('/upload', [StopwatchController::class, 'handleUpload'])->name('file.upload');


Route::get('/uploaded-file', [StopwatchController::class, 'show'])->name('show.uploaded.file');

Route::get('/email-form', function () {
    return view('email_form'); 
})->name('email_form');



Route::get('/clock', [StopwatchController::class, 'showClock'])->name('show.clock');



Route::get('/stopwatch', [StopwatchController::class, 'index'])->name('stopwatch.index');
Route::post('/stopwatch/save-time', [StopwatchController::class, 'saveTime'])->name('stopwatch.saveTime');
Route::get('/stopwatch/saved-times', [StopwatchController::class, 'showSavedTimes'])->name('stopwatch.showSavedTimes');
Route::get('/stopwatch/generate-pdf', [StopwatchController::class, 'generatePdf'])->name('stopwatch.generatePdf');
Route::post('/stopwatch/email-pdf', [StopwatchController::class, 'emailPdf'])->name('stopwatch.emailPdf');








Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

require __DIR__.'/auth.php';
