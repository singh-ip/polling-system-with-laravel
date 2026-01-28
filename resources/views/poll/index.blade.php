<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Polls - Admin</title>
    <style>body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:40px}</style>
</head>
<body>
    <h1>Polls</h1>
    <ul>
        @foreach($polls as $poll)
            <li>
                <a href="{{ url('/polls/'.$poll->id) }}">{{ $poll->question }}</a>
                — votes: {{ $poll->votes_count }}
            </li>
        @endforeach
    </ul>
</body>
</html>
