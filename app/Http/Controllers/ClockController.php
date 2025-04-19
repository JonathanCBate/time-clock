<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCSVEmail;


class ClockController extends Controller
{
    public function addTime(Request $request)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);
    
        $now = Carbon::now();
        $duration = $request->minutes * 60; // seconds
    
        WorkLog::create([
            'user_id' => auth()->id(),
            'elapsed_time' => $duration,
            'work_description' => trim($request->description) ?: 'No description provided',
            'start_time' => null,
            'end_time' => null,
            'logged_date' => $now->toDateString(),
            'is_manual' => true,
        ]);
    
        return back()->with('success', 'Time added!');
    }
    
    public function index()
    {
        $workLogs = $this->getWorkLogsForWeek();
        $totalWeeklyTime = gmdate('H:i:s', $workLogs->sum('elapsed_time') ?? 0);

        $totalTimeForToday = gmdate('H:i:s', $this->getTotalWorkTimeForToday());

        return view('dashboard', compact('workLogs', 'totalWeeklyTime', 'totalTimeForToday'));
    }

    public function post(Request $request)
{
    $request->validate([
        'work_description' => 'nullable|string',
        'elapsed_time' => 'required|integer|min:1'
    ]);

    $workLog = WorkLog::create([
        'user_id' => auth()->id(),
        'work_description' => $request->work_description ?: 'No description provided',
        'elapsed_time' => $request->elapsed_time,
        'logged_date' => now()->toDateString(),
    ]);

    return response()->json([
        'success' => true,
        'work_description' => $workLog->work_description,
        'created_at' => $workLog->created_at->format('l, F j, Y g:i A'),
        'elapsed_time' => $workLog->elapsed_time,
        'totalWeeklyTime' => gmdate('H:i:s', $this->getTotalWorkTimeForWeek()),
        'totalWeeklyTimeSeconds' => $this->getTotalWorkTimeForWeek(),
        'totalTimeForToday' => gmdate('H:i:s', $this->getTotalWorkTimeForToday()),
        'totalTimeForTodaySeconds' => $this->getTotalWorkTimeForToday(),
    ]);
}


    public function store(Request $request)
    {
        $workLog = WorkLog::create([
            'work_description' => $request->work_description,
            'elapsed_time' => $request->elapsed_time,
            'start_time' => now(),
            'end_time' => now()->addSeconds($request->elapsed_time)
        ]);

        return response()->json([
            'success' => true,
            'work_description' => $workLog->work_description,
            'created_at' => $workLog->created_at->format('l, F j, Y g:i A'),
            'elapsed_time' => $workLog->elapsed_time,
            'totalTimeForToday' => gmdate('H:i:s', $this->getTotalWorkTimeForToday()),
            'totalWeeklyTime' => gmdate('H:i:s', $this->getTotalWorkTimeForWeek()),
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'logs' => 'required|array',
            'logs.*' => 'exists:work_logs,id',
        ]);

        WorkLog::whereIn('id', $request->logs)->delete();

        return redirect()->back()->with('success', 'Selected work logs have been deleted.');
    }

    public function calendar(Request $request)
    {
        $currentDate = $request->input('week') ? Carbon::parse($request->input('week')) : Carbon::now();
        $workLogs = $this->getWorkLogsForWeek($currentDate);
        $startOfWeek = $currentDate->copy()->startOfWeek();
        $endOfWeek = $currentDate->copy()->endOfWeek();
        $totalWeeklyTime = gmdate('H:i:s', $workLogs->sum('elapsed_time') ?? 0);

        $days = $this->getDailyTotals($workLogs, $startOfWeek);

        return view('work-clock.calendar', [
            'workLogs' => $workLogs,
            'totalWeeklyTime' => $totalWeeklyTime,
            'days' => $days,
            'previousWeek' => $startOfWeek->copy()->subWeek()->format('Y-m-d'),
            'nextWeek' => $startOfWeek->copy()->addWeek()->format('Y-m-d'),
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'currentDate' => $currentDate
        ]);
    }

    public function sendCSVEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        $date = Carbon::now();
        $workLogs = $this->getWorkLogsForWeek($date);
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        $days = $this->getDailyTotals($workLogs, $startOfWeek);

        $tempFilePath = storage_path('app/public/work_time_report.csv');
        $handle = fopen($tempFilePath, 'w+');

        $this->buildCSVReport($handle, $date, $startOfWeek, $endOfWeek, $workLogs, $days);
        fclose($handle);

        Mail::to($email)->send(new SendCSVEmail($tempFilePath));
        if (file_exists($tempFilePath)) unlink($tempFilePath);

        return redirect()->back()->with('message', 'CSV sent successfully!');
    }

    public function generateCSV()
    {
        $date = Carbon::now();
        $workLogs = $this->getWorkLogsForWeek($date);
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        $days = $this->getDailyTotals($workLogs, $startOfWeek);

        $handle = fopen('php://temp', 'r+');
        $this->buildCSVReport($handle, $date, $startOfWeek, $endOfWeek, $workLogs, $days);
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="work_time_report.csv"',
        ]);
    }

    // =======================
    // Reusable Helper Methods
    // =======================

    private function getWorkLogsForWeek(Carbon $date = null)
    {
        $date = $date ?: Carbon::now();
        return WorkLog::whereBetween('created_at', [
            $date->copy()->startOfWeek(), 
            $date->copy()->endOfWeek()
        ])->get();
    }

    private function getDailyTotals($workLogs, $startOfWeek)
    {
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dailyTime = $workLogs->whereBetween('created_at', [
                $day->copy()->startOfDay(), $day->copy()->endOfDay()
            ])->sum('elapsed_time');

            $days[] = [
                'date' => $day,
                'totalTime' => gmdate('H:i:s', $dailyTime)
            ];
        }
        return $days;
    }

    private function buildCSVReport($handle, $currentDate, $startOfWeek, $endOfWeek, $workLogs, $days)
    {
        fputcsv($handle, ['=== Work Summary ===']);
        fputcsv($handle, ['Report Date:', $currentDate->format('F j, Y')]);
        fputcsv($handle, ['Start of Week:', $startOfWeek->format('l, F j, Y')]);
        fputcsv($handle, ['End of Week:', $endOfWeek->format('l, F j, Y')]);
        fputcsv($handle, ['Total Time This Week:', gmdate('H:i:s', $workLogs->sum('elapsed_time') ?? 0)]);

        fputcsv($handle, []);

        fputcsv($handle, ['=== Daily Totals ===']);
        fputcsv($handle, ['Date', 'Day', 'Total Time']);
        foreach ($days as $day) {
            fputcsv($handle, [
                $day['date']->toDateString(),
                $day['date']->format('l'),
                $day['totalTime']
            ]);
        }
        fputcsv($handle, []);

        fputcsv($handle, ['=== Detailed Work Logs ===']);
        fputcsv($handle, ['Date', 'Day', 'Elapsed Time', 'Description']);
        foreach ($workLogs as $log) {
            fputcsv($handle, [
                $log->created_at->toDateString(),
                $log->created_at->format('l'),
                
                gmdate('H:i:s', (int) $log->elapsed_time ?? 0),


                $log->work_description ?: 'No description'
            ]);
        }
    }

    private function getTotalWorkTimeForToday()
    {
        return WorkLog::whereDate('created_at', Carbon::today())->sum('elapsed_time');
    }

    private function getTotalWorkTimeForWeek()
    {
        return WorkLog::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('elapsed_time');
    }

    public function formatTime($seconds)
    {
        return sprintf("%d hours %d minutes", floor($seconds / 3600), floor(($seconds % 3600) / 60));
    }
}
