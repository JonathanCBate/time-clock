
<!DOCTYPE html>
<html>
<head>
    <title>Saved Times</title>
</head>
<body>
    <h1>Saved Times</h1>
    <ul>
        @foreach ($savedTimes as $time)
            <li>{{ $time->time }}</li>
        @endforeach
    </ul>
</body>
</html>
