<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Polls - Admin</title>
    <style>
        body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:40px}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;border-bottom:2px solid #ddd;padding-bottom:20px}
        .header h1{margin:0}
        .user-info{text-align:right;font-size:14px}
        .user-info p{margin:5px 0;color:#666}
        .logout-btn{background:#dc3545;color:white;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;text-decoration:none;display:inline-block;margin-top:10px;transition:background 0.2s}
        .logout-btn:hover{background:#c82333}
        .pagination{margin-top:20px;text-align:center}
        .pagination a, .pagination span{padding:5px 10px;margin:0 2px;border:1px solid #ddd;text-decoration:none}
        .pagination .active span{background:#007bff;color:white;border-color:#007bff}
        .polls-list{margin:20px 0}
    </style>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>
</head>
<script>
Pusher.logToConsole = false;

const echo = new window.Echo({
    broadcaster: 'pusher',
    key: '{{ config("broadcasting.connections.pusher.key") }}',
    wsHost: '{{ config("broadcasting.connections.pusher.options.host") }}',
    wsPort: '{{ config("broadcasting.connections.pusher.options.port") }}',
    wssPort: 443,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
});

echo.channel('polls').listen('VoteCast', (e) => {
    const pollEl = document.querySelector(`[data-poll-id="${e.poll_id}"] .votes`);
    if (pollEl) {
        pollEl.textContent = (e.votes_count !== undefined) ? e.votes_count : e.total_votes;
    }
});
</script>
<body>
    <div class="header">
        <h1>Polls</h1>
        <div class="user-info">
            <p><strong>{{ auth()->user()->name }}</strong></p>
            <p>{{ auth()->user()->email }}</p>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="polls-list">
        @if($polls->count() > 0)
            <ul>
                @foreach($polls as $poll)
                    <li data-poll-id="{{ $poll->id }}">
                        <a href="{{ url('/polls/'.$poll->id) }}">{{ $poll->question }}</a>
                        — votes: <span class="votes">{{ $poll->votes_count }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p>No polls found.</p>
        @endif
    </div>
    <div class="pagination">
        {{ $polls->links('pagination::bootstrap-4') }}
    </div>
</body>
</html>
