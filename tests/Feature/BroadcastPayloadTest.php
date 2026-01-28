<?php

namespace Tests\Feature;

use App\Events\VoteCast;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class BroadcastPayloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_cast_event_includes_correct_data()
    {
        Event::fake([VoteCast::class]);

        $poll = Poll::create(['question' => 'Payload test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'A']);

        $this->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $option->id]);

        Event::assertDispatched(VoteCast::class, function ($event) use ($poll, $option) {
            return $event->poll->id === $poll->id 
                && $event->option->id === $option->id;
        });
    }

    public function test_vote_cast_broadcast_with_returns_option_id()
    {
        $poll = Poll::create(['question' => 'Broadcast data?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'X']);

        $event = new VoteCast($poll, $option);
        $data = $event->broadcastWith();

        $this->assertArrayHasKey('option_id', $data);
        $this->assertEquals($option->id, $data['option_id']);
    }

    public function test_vote_cast_broadcast_with_returns_votes_count()
    {
        $poll = Poll::create(['question' => 'Count broadcast?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Y', 'votes_count' => 5]);

        $event = new VoteCast($poll, $option);
        $data = $event->broadcastWith();

        $this->assertArrayHasKey('votes_count', $data);
        $this->assertEquals(5, $data['votes_count']);
    }

    public function test_vote_cast_broadcast_with_returns_poll_id()
    {
        $poll = Poll::create(['question' => 'Poll ID broadcast?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Z']);

        $event = new VoteCast($poll, $option);
        $data = $event->broadcastWith();

        $this->assertArrayHasKey('poll_id', $data);
        $this->assertEquals($poll->id, $data['poll_id']);
    }

    public function test_vote_cast_broadcasts_on_correct_channel()
    {
        $poll = Poll::create(['question' => 'Channel test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'A']);

        $event = new VoteCast($poll, $option);
        $channel = $event->broadcastOn();

        $this->assertEquals('poll.' . $poll->id, $channel->name);
    }
}
