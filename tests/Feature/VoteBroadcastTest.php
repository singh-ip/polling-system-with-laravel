<?php

namespace Tests\Feature;

use App\Events\VoteCast;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VoteBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_dispatches_vote_cast_event()
    {
        Event::fake([VoteCast::class]);

        $poll = Poll::create(['question' => 'Broadcast test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'A']);

        $response = $this->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $option->id]);
        $response->assertStatus(200);

        Event::assertDispatched(VoteCast::class, function ($event) use ($poll, $option) {
            return $event->poll->id === $poll->id && $event->option->id === $option->id;
        });
    }
}
