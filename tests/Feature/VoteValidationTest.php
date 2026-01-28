<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class VoteValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_rejects_missing_option_id()
    {
        $poll = Poll::create(['question' => 'Validation test?']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'X']);

        $response = $this->postJson('/polls/' . $poll->id . '/vote', []);

        $response->assertStatus(422);
    }

    public function test_vote_rejects_invalid_option_id()
    {
        $poll = Poll::create(['question' => 'Invalid option test?']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'X']);

        $response = $this->postJson('/polls/' . $poll->id . '/vote', ['option_id' => 9999]);

        $response->assertStatus(422);
    }

    public function test_vote_rejects_option_from_different_poll()
    {
        $poll1 = Poll::create(['question' => 'Poll 1?']);
        $opt1 = PollOption::create(['poll_id' => $poll1->id, 'label' => 'A']);

        $poll2 = Poll::create(['question' => 'Poll 2?']);
        PollOption::create(['poll_id' => $poll2->id, 'label' => 'B']);

        $this->assertDatabaseHas('polls', ['id' => $poll2->id, 'question' => 'Poll 2?']);
        $this->assertDatabaseHas('poll_options', ['id' => $opt1->id, 'poll_id' => $poll1->id]);
    }

    public function test_vote_accepts_valid_option_id()
    {
        $poll = Poll::create(['question' => 'Valid test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'X']);

        $response = $this->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $option->id]);

        $response->assertStatus(200);
    }

    public function test_vote_returns_nonexistent_poll_404()
    {
        $response = $this->postJson('/polls/9999/vote', ['option_id' => 1]);

        $response->assertStatus(404);
    }
}
