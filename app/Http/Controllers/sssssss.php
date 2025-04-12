<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class StopwatchController extends Controller
{
    public function saveColor(Request $request)
    {
        $request->validate([
            'color' => 'required|string',
        ]);

        Session::put('color', $request->input('color'));

        return back()->with('success', 'Color saved successfully!');
    }

    public function index()
    {
        $savedTimes = Session::get('savedTimes', []);
        return view('stopwatch.index', compact('savedTimes'));
    }

    public function showUploadForm()
    {
        return view('upload');
    }

    public function handleUpload(Request $request)
    {
        \Log::info("File upload request received.");
    
        if (!$request->hasFile('file')) {
            \Log::error("No file found in request.");
            return back()->with('error', 'No file was uploaded.');
        }
    
        $request->validate([
            'file' => 'required|mimes:jpg,png,jpeg|max:2048',
        ]);
    
        $file = $request->file('file');
        \Log::info("File retrieved: " . $file->getClientOriginalName());
    
        try {
            $filePath = $file->store('uploads', 'public');
            \Log::info("File stored at: " . $filePath);
    
            // Store file path in session and log it
            Session::put('uploadedFile', $filePath);
            \Log::info("Session stored file path: " . Session::get('uploadedFile'));
    
            return redirect()->route('show.clock')->with('success', 'File uploaded successfully!');
        } catch (\Exception $e) {
            \Log::error("File upload failed: " . $e->getMessage());
            return back()->with('error', 'File upload failed.');
        }
    }
    


    public function showSavedTimes()
    {
        $savedTimes = Session::get('savedTimes', []);
        return view('stopwatch.saved-times', compact('savedTimes'));
    }

    public function clearSavedTimes()
    {
        Session::forget('savedTimes');
        return response()->json(['message' => 'Saved times cleared successfully!']);
    }

    public function emailForm()
    {
        return view('email_form');
    }

    public function showClock()
{
    $uploadedFile = Session::get('uploadedFile', null); // Get uploaded file path
    $color = Session::get('color', 'white'); // Default background color

    \Log::info("Loaded session file path: " . ($uploadedFile ?? 'None'));
    \Log::info("Loaded session color: " . ($color ?? 'None'));

    return view('clock_app.index', compact('uploadedFile', 'color'));
}

    

    public function generatePdf()
    {
        $savedTimes = Session::get('savedTimes', []);
        $pdf = Pdf::loadView('stopwatch.pdf', compact('savedTimes'));
        return $pdf->download('saved_times.pdf');
    }

    public function emailPdf(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $savedTimes = Session::get('savedTimes', []);
        $pdf = Pdf::loadView('stopwatch.pdf', compact('savedTimes'));

        Mail::send('emails.saved_times', [], function ($message) use ($email, $pdf) {
            $message->to($email)
                    ->subject('Your Saved Times PDF')
                    ->attachData($pdf->output(), 'saved_times.pdf');
        });

        return redirect()->back()->with('success', 'Email sent successfully!');
    }
}
