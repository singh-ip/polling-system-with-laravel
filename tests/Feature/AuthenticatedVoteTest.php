<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_vote_only_once()
    {
        $user = User::factory()->create();

        $poll = Poll::create(['question' => 'Auth vote?']);
        $a = PollOption::create(['poll_id' => $poll->id, 'label' => 'One']);
        $b = PollOption::create(['poll_id' => $poll->id, 'label' => 'Two']);

        $response = $this->actingAs($user)->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $a->id]);
        $response->assertStatus(200);

        $response2 = $this->actingAs($user)->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $b->id]);
        $response2->assertStatus(422);
    }
}
