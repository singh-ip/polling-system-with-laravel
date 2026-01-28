<?php

namespace Tests\Unit;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use App\Services\PollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_vote_creates_vote_and_increments_option()
    {
        $service = new PollService();

        $poll = Poll::create(['question' => 'Service test?']);
        $opt = PollOption::create(['poll_id' => $poll->id, 'label' => 'X']);

        $service->submitVote($poll, $opt->id, null, '127.0.0.1');

        $this->assertDatabaseHas('votes', ['poll_id' => $poll->id, 'poll_option_id' => $opt->id]);
        $this->assertEquals(1, $opt->fresh()->votes_count);
    }

    public function test_submit_vote_throws_when_already_voted()
    {
        $this->expectException(\DomainException::class);

        $service = new PollService();

        $poll = Poll::create(['question' => 'Service duplicate?']);
        $opt = PollOption::create(['poll_id' => $poll->id, 'label' => 'Y']);

        $service->submitVote($poll, $opt->id, null, '10.0.0.1');
        $service->submitVote($poll, $opt->id, null, '10.0.0.1');
    }
}
