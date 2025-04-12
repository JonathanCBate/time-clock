<!DOCTYPE html>
<html lang="en">
<head>
@extends('layouts.app')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Saved Times</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #282c34;
            color: white;
        }
        .saved-times {
            width: 300px;
            height: 200px;
            border: 1px solid #fff;
            margin: 20px auto;
            overflow-y: auto;
            padding: 10px;
            background: #444;
        }
        button {
            font-size: 1em;
            margin: 10px;
            padding: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h1>Saved Times</h1>
    <div class="saved-times">
        @foreach($savedTimes as $time)
            <p>{{ $time }}</p>
        @endforeach
    </div>

    <button onclick="clearSavedTimes()">Clear All</button>
    <a href="{{ route('make-pdf') }}" target="_blank">
        <button>Download PDF</button>
    </a>

    <script>
        function clearSavedTimes() {
            fetch('/clear-saved-times', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(error => console.error("Error:", error));
        }
    </script>

</body>
</html>
