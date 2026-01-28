<?php

namespace App\Events;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

final class VoteCast implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public Poll $poll;
    public PollOption $option;

    public function __construct(Poll $poll, PollOption $option)
    {
        $this->poll = $poll;
        $this->option = $option;
    }

    public function broadcastOn()
    {
        return [
            new Channel('poll.' . $this->poll->id),
            new Channel('polls'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'option_id' => $this->option->id,
            'votes_count' => $this->option->fresh()->votes_count,
            'poll_id' => $this->poll->id,
        ];
    }
}
