<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Work Log PDF</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        h1, h2 { color: #444; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Work Calendar for the Week of {{ $currentDate->format('F j, Y') }}</h1>
    <p>Start of the Week: {{ $startOfWeek->format('l, F j, Y') }}</p>
    <p>End of the Week: {{ $endOfWeek->format('l, F j, Y') }}</p>
    <p>Total Time This Week: {{ $totalWeeklyTime }}</p>

    <h2>Daily Logs</h2>
    <ul>
        @foreach ($days as $day)
            <li>{{ $day['date']->format('l, F j, Y') }} - Total Time: {{ $day['totalTime'] }}</li>
        @endforeach
    </ul>

    <h2>Work Logs for the Week</h2>
    <ul>
        @foreach ($workLogs as $log)
            <li>
                {{ $log->work_description ?: 'No description provided' }} - 
                {{ $log->created_at->format('l, F j, Y g:i A') }} - 
                {{ gmdate('H:i:s', $log->elapsed_time) }}
            </li>
        @endforeach
    </ul>
</body>
</html>
