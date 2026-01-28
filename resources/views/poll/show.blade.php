<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Poll - {{ $poll->question }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:40px}
        .option{margin:8px 0}
    </style>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>
</head>
<body>
    <h1>{{ $poll->question }}</h1>

    <div id="poll">
        @foreach($poll->options as $option)
            <div class="option" data-id="{{ $option->id }}">
                <button class="vote-btn" data-id="{{ $option->id }}">Vote</button>
                <strong>{{ $option->label }}</strong>
                — <span class="count">{{ $option->votes_count }}</span>
            </div>
        @endforeach
    </div>

    <p>Shareable link: <input readonly value="{{ url('/polls/'.$poll->id) }}" style="min-width:300px"></p>

    <script>
        const pollId = {{ $poll->id }};
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.querySelectorAll('.vote-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const optionId = btn.getAttribute('data-id');
                try {
                    const res = await fetch(`/polls/${pollId}/vote`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ option_id: optionId })
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Vote failed');
                    alert(json.message);
                } catch (err) {
                    alert(err.message || 'Error');
                }
            });
        });

        Pusher.logToConsole = false;
        const echo = new window.Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key") ?? env("PUSHER_APP_KEY") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") ?? env("PUSHER_APP_CLUSTER") }}',
            wsHost: '127.0.0.1',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss']
        });

        echo.channel('poll.' + pollId).listen('VoteCast', (e) => {
            const el = document.querySelector(`[data-id="${e.option_id}"] .count`);
            if (el) el.textContent = e.votes_count;
        });
    </script>
</body>
</html>
