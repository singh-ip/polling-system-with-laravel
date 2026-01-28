<?php

namespace Tests\Unit;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class VoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_can_be_created()
    {
        $poll = Poll::create(['question' => 'Test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes']);

        $vote = Vote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertDatabaseHas('votes', ['id' => $vote->id]);
    }

    public function test_vote_belongs_to_poll()
    {
        $poll = Poll::create(['question' => 'Test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes']);
        $vote = Vote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertEquals($poll->id, $vote->poll->id);
    }

    public function test_vote_belongs_to_option()
    {
        $poll = Poll::create(['question' => 'Test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes']);
        $vote = Vote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertEquals($option->id, $vote->option->id);
    }

    public function test_vote_tracks_ip_address()
    {
        $poll = Poll::create(['question' => 'Test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes']);
        $vote = Vote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'ip_address' => '192.168.1.1',
        ]);

        $this->assertEquals('192.168.1.1', $vote->ip_address);
    }

    public function test_vote_tracks_user_id()
    {
        $user = User::factory()->create();
        $poll = Poll::create(['question' => 'Test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes']);
        
        $vote = Vote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertEquals($user->id, $vote->user_id);
    }
}
