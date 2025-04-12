
@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stopwatch</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #282c34;
            color: white;
            flex-direction: column;
        }
        .stopwatch {
            font-size: 3em;
        }
        button {
            font-size: 1em;
            margin: 5px;
            padding: 10px;
        }
        .saved-times {
            width: 300px;
            height: 200px;
            border: 1px solid #fff;
            margin-top: 20px;
            overflow-y: auto;
            padding: 10px;
        }
    </style>
</head>
<body>

    <div class="stopwatch" id="stopwatch">00:00:00.000</div>
    <button id="toggleButton" onclick="toggleStopwatch()">Start</button>
    <button onclick="resetStopwatch()">Reset</button>
    <button onclick="clearSavedTimes()">Clear Saved Times</button>
    <button onclick="printTime()">Save Time</button>

    <h2>Saved Times</h2>
    <div class="saved-times" id="savedTimes"></div>

    @php
        $savedTimes = session('savedTimes', []);
    @endphp

    <script>
        let timer;
        let elapsedTime = 0;
        let running = false;
        let savedTimes = @json($savedTimes); // Get session data

        function formatTime(ms) {
            let totalSeconds = Math.floor(ms / 1000);
            let hours = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
            let minutes = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
            let seconds = String(totalSeconds % 60).padStart(2, '0');
            let milliseconds = String(ms % 1000).padStart(3, '0');
            return `${hours}:${minutes}:${seconds}.${milliseconds}`;
        }

        function toggleStopwatch() {
            if (running) {
                stopStopwatch();
            } else {
                startStopwatch();
            }
        }

        function printTime() {
            const formattedTime = formatTime(elapsedTime);
            savedTimes.push(formattedTime);
            displaySavedTimes();

            // ✅ Save time to Laravel session
            saveTimeToServer(formattedTime);
        }

        function displaySavedTimes() {
            const savedTimesDiv = document.getElementById("savedTimes");
            savedTimesDiv.innerHTML = savedTimes.join('<br>');
        }

        function startStopwatch() {
            running = true;
            let startTime = Date.now() - elapsedTime;
            timer = setInterval(() => {
                elapsedTime = Date.now() - startTime;
                document.getElementById('stopwatch').textContent = formatTime(elapsedTime);
            }, 10);
            document.getElementById('toggleButton').textContent = 'Stop';
        }

        function stopStopwatch() {
            clearInterval(timer);
            running = false;
            document.getElementById('toggleButton').textContent = 'Start';
        }

        function resetStopwatch() {
            clearInterval(timer);
            running = false;
            elapsedTime = 0;
            document.getElementById('stopwatch').textContent = "00:00:00.000";
            document.getElementById('toggleButton').textContent = 'Start';
        }

        function clearSavedTimes() {
            savedTimes = [];
            displaySavedTimes();

            // ✅ Clear saved times from Laravel session
            fetch('/clear-saved-times', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => console.log("Cleared times on server:", data))
            .catch(error => console.error("Error:", error));
        }

        function saveTimeToServer(time) {
            fetch('/save-time', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ time: time })
            })
            .then(response => response.json())
            .then(data => console.log("Time saved to Laravel:", data))
            .catch(error => console.error("Error:", error));
        }

        window.onload = displaySavedTimes;
    </script>

</body>
</html>
 
@endsection
