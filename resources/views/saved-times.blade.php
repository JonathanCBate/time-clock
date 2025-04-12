<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Stopwatch Times</title>
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
        .saved-times {
            width: 300px;
            max-height: 200px;
            border: 1px solid #fff;
            margin-top: 20px;
            overflow-y: auto;
            padding: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            padding: 5px 0;
            border-bottom: 1px solid #444;
        }
    </style>
</head>
<body>
@extends('layouts.app')
    <h1>Saved Times</h1>
    <div class="saved-times">
        @if($savedTimes->isEmpty())
            <p>No saved times.</p>
        @else
            <ul>
                @foreach ($savedTimes as $time)
                    <li>{{ $time->time }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</body>
</html>
