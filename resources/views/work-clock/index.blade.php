@extends('layouts.app')

@section('title', 'Work Clock')

@push('styles')
    <style>
        body {
            color: white;
        }

        .container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .aligned-btn {
            padding: 10px 20px;
            font-size: 16px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgb(187, 17, 17);
            color: white;
            border: none;
            display: flex;
            justify-content: center; 
            align-items: center;
            cursor: pointer;
        }

        .custom-box {
            margin-left: 100px; 
            color: white;
            padding: 10px;
        }

        #work-form {
            display: none;
            margin-top: 20px;
        }

        #work-log-container {
            max-height: 300px; 
            overflow-y: auto; 
            padding: 10px; 
            border: 1px solid #ccc;
            background-color: #333;
        }

        /* Removed display: none from #work-log to show logs */
        #work-log {
            list-style: disc;
            padding-left: 20px;
        }
    </style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<h1 style="text-align:center; font-size:50px;">Record Your Work Time</h1><br><br>

<h2 style="font-size:30px;">Today's date is: <span id="date"></span></h2>
<p class="custom-box" style="font-size:25px;">Today is: <span id="current-day"></span></p>

<section>
    <h2>Total Work Time This Week</h2>
    <p style="color: red; font-size: 2rem;" id="weekly-total-time">{{ $totalWeeklyTime }}</p>
</section>

<section>
    <h2>Total Work Time Today</h2>
    <p id="total-time-today" style="font-size: 2rem;">{{ $totalTimeForToday }}</p>
</section>

<div style="text-align:center;" class="container">
    <p>Press this button to record your work time</p>
    <button id="button" class="aligned-btn">Start</button>
    <h2 id="clock" style="font-size:40px;">00:00:00</h2>
    <button onclick="addTime()">Add time</button>
</div>
<div id="addTime"></div>
<!-- Work Log Form (no method needed since JS handles it) -->
<form id="work-form">
    @csrf
    <div id="work-details">
        <label style='color:white;'>What are you doing?</label>
        <input style='color:black;' type='text' name='work_description' />
    </div>
</form>

<!-- Work Log List -->
<ul id="work-log"></ul>

@if(session('success'))
    <div class="alert alert-success" style="color: green; font-size: 1.2rem;">
        {{ session('success') }}
    </div>
@endif

<!-- Bulk Delete Form -->
<form action="{{ route('work-log.bulk-delete') }}" method="POST">
    @csrf
    @foreach($workLogs as $log)
        <label>
            <input type="checkbox" name="logs[]" value="{{ $log->id }}">
            {{ e($log->work_description ?: 'No description provided') }} - 
            {{ $log->created_at->format('l, F j, Y g:i A') }} - 
            {{ gmdate('H:i:s', $log->elapsed_time) }}
        </label><br>
    @endforeach
    <button type="submit" onclick="return confirm('Are you sure you want to delete selected logs?')">Delete Selected</button>
</form>

<script>
    function addTime() {
    let container = document.getElementById('addTime');

    if (container.innerHTML === '') {
        container.innerHTML = `
         <form  method="POST" action="{{ route('work-logs.add-time') }}">
            @csrf
            <div>
                <label>Time to Add (in minutes)</label>
                <input style="color:black; type="number" name="minutes" min="1" required>
            </div>

            <div>
                <label>Description (optional)</label>
                <input style="color:black; type="text" name="description">
            </div>

            <button type="submit">Add Time</button>
        </form>   
        `;
    } else {
        container.innerHTML = ''; 
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let startTime = null; 
    let elapsedTime = 0;
    let timer = null;

    const button = document.getElementById("button");

    function formatTime(seconds) {
        if (isNaN(seconds)) return "00:00:00";
        let hours = Math.floor(seconds / 3600);
        let minutes = Math.floor((seconds % 3600) / 60);
        let secs = Math.floor(seconds % 60);
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    function startClock() {
        elapsedTime = 0;
        startTime = Date.now();
        timer = setInterval(() => {
            elapsedTime = Math.floor((Date.now() - startTime) / 1000);
            document.getElementById("clock").innerText = formatTime(elapsedTime);
        }, 1000);
        document.getElementById("work-form").style.display = "block";
    }

    function stopClock() {
        clearInterval(timer);
        elapsedTime = Math.floor((Date.now() - startTime) / 1000);
        document.getElementById("clock").innerText = "00:00:00";
        button.disabled = true;
        submitWorkLog();
    }

    function submitWorkLog() {
        let workDescription = document.querySelector("input[name='work_description']").value.trim();

        let logData = {
            work_description: workDescription || "No description provided",
            elapsed_time: elapsedTime
        };

        fetch("{{ route('post_data') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: new URLSearchParams(logData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWorkLogList(data);
            } else {
                console.error("Failed to log work:", data);
            }
        })
        .catch(error => console.error("Error:", error))
        .finally(() => {
            document.getElementById("work-form").reset();
            document.getElementById("work-form").style.display = "none";
            button.disabled = false;
        });
    }

    function updateWorkLogList(data) {
        let workLogList = document.getElementById("work-log");

        let newLog = document.createElement("li");
        newLog.innerText = `${data.work_description} - ${data.created_at} - ${formatTime(data.elapsed_time)}`;
        workLogList.appendChild(newLog);
        workLogList.scrollTop = workLogList.scrollHeight;

        // Update weekly total
        if (data.totalWeeklyTime !== undefined) {
            document.getElementById("weekly-total-time").innerText = data.totalWeeklyTime;
        }

        // Update daily total
        if (data.totalTimeForToday !== undefined) {
            document.getElementById("total-time-today").innerText = data.totalTimeForToday;
        }
    }

    button.addEventListener("click", function () {
        if (this.textContent === "Start") {
            this.textContent = "Finish";
            startClock();
        } else {
            this.textContent = "Start";
            stopClock();
        }
    });

    // Update today's date and current day in the UI
    document.getElementById("date").innerText = new Date().toLocaleDateString();
    document.getElementById("current-day").innerText = new Date().toLocaleString('en-US', { weekday: 'long' });
});
</script>
@endsection
