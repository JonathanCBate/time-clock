@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Saved Times</title>

    <style>
        /* Set the text color to white for the entire page */
        body {
            color: white;
            background-color: #282c34; /* Optional: setting background color to dark for better contrast */
            font-family: Arial, sans-serif;
        }

        h1 {
            color: white;
        }

        label, button {
            color: white;
            font-size: 1em;
        }
        input {
            color: black;
        }

        input[type="email"], button {
            padding: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h1>Email Saved Times</h1>

    <!-- Form for sending email -->
    <form id="emailForm" action="{{ route('email.pdf') }}" method="post">
        @csrf
        <label for="email">Enter your email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Send Email</button>
    </form>

</body>
</html>
@endsection
