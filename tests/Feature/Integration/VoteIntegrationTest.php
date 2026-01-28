<?php

namespace Tests\Feature\Integration;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_vote_flow_persists_counts()
    {
        $poll = Poll::create(['question' => 'Integration flow?']);
        $opt = PollOption::create(['poll_id' => $poll->id, 'label' => 'Z']);

        $res = $this->postJson('/polls/' . $poll->id . '/vote', ['option_id' => $opt->id]);
        $res->assertStatus(200);

        $this->assertDatabaseHas('poll_options', ['id' => $opt->id, 'votes_count' => 1]);
        $this->assertDatabaseHas('votes', ['poll_id' => $poll->id, 'poll_option_id' => $opt->id]);
    }
}
