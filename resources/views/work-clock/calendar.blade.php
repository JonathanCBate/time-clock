@extends('layouts.app')

@section('title', 'Work Calendar')
@push('styles')
    <style>
        body {
            color: white;
        }

       
    </style>
@endpush
@section('content')

<div style="color: white;">
    <p>Start of the Week: {{ $startOfWeek->format('l, F j, Y') }}</p>
    <p>End of the Week: {{ $endOfWeek->format('l, F j, Y') }}</p>
    <p>Total Time This Week: {{ $totalWeeklyTime }}</p>

    <a href="{{ route('calendar', ['week' => $previousWeek]) }}" style="color: lightblue;">Previous Week</a> |
    <a href="{{ route('calendar', ['week' => $nextWeek]) }}" style="color: lightblue;">Next Week</a>

    <br>
    <h2>Daily Logs</h2><br>
    <ul>
        @foreach ($days as $day)
            <li>{{ $day['date']->format('l, F j, Y') }} - Total Time: {{ $day['totalTime'] }}</li>
            <br>
        @endforeach
    </ul>

    <h2>Work Logs for the Week</h2>

    <!-- Scrollable Container -->
    <div style="max-height: 200px; overflow-y: auto; border: 1px solid white; padding: 10px; border-radius: 5px;">
        <ul>
            @foreach ($workLogs as $log)
                <li>
                    {{ $log->work_description ?: 'No description provided' }} -
                    {{ $log->created_at->format('l, F j, Y g:i A') }} -
                    {{ gmdate('H:i:s', $log->elapsed_time) }}
                </li>
            @endforeach
        </ul>
    </div>

    <br>
    <div id="form-container" style="margin-top: 20px; color: white;"></div>
    
    <button>
        <a style="color: red; margin: 20px;" href="{{ route('generate_pdf') }}" class="aligned-btn">Generate CSV</a>
    </button>
    
    <button style="color: red;" onclick="ImportantForm()">Email CSV</button>
</div>

<script>
    function ImportantForm() {
        // Get the container where the form will be inserted
        let container = document.getElementById('form-container');

        // Check if the form already exists, if so, don't add another one
        if (container.innerHTML === '') {
            // Set the HTML content for the form
            container.innerHTML = `
                <form action="{{ route('send_pdf_email') }}" method="POST">
                    @csrf
                    <label for="email">Enter your email:</label>
                    <input style="color:black;" type="email" name="email" placeholder="Enter Your email" required>
                    <button type="submit" style="color: red;">Send CSV</button>
                </form>
            `;
        } else {
            // If the form is already displayed, hide it
            container.innerHTML = ''; // This will hide the form when clicked again
        }
    }
</script>

@endsection
