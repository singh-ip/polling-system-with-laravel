<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_vote_and_cannot_vote_twice_from_same_ip()
    {
        $poll = Poll::create(['question' => 'Favorite color?']);
        $optA = PollOption::create(['poll_id' => $poll->id, 'label' => 'Red']);
        $optB = PollOption::create(['poll_id' => $poll->id, 'label' => 'Blue']);

        $response = $this->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $optA->id]);
        $response->assertStatus(200);

        $response2 = $this->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $optB->id]);
        $response2->assertStatus(422);
    }
}
