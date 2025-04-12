<!DOCTYPE html>
<html>
<head>
    <title>Saved Times PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
        }
        th {
            background: #ddd;
        }
    </style>
</head>
<h1>Saved Times</h1>
    <div class="saved-times">
        @foreach($savedTimes as $time)
            <p>{{ $time }}</p>
        @endforeach
    </div>

    
    

</body>
</html>
