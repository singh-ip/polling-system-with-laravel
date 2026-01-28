<?php

namespace Tests\Unit;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use App\Services\PollService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PollServiceGetMethodsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_poll_with_options_loads_poll_and_options()
    {
        $service = new PollService();

        $poll = Poll::create(['question' => 'Service test?']);
        $opt1 = PollOption::create(['poll_id' => $poll->id, 'label' => 'A']);
        $opt2 = PollOption::create(['poll_id' => $poll->id, 'label' => 'B']);

        $result = $service->getPollWithOptions($poll);

        $this->assertEquals($poll->id, $result->id);
        $this->assertEquals(2, $result->options->count());
        $this->assertTrue($result->options->pluck('id')->contains($opt1->id));
    }

    public function test_list_polls_returns_all_polls_with_vote_counts()
    {
        $service = new PollService();

        $poll1 = Poll::create(['question' => 'First?']);
        PollOption::create(['poll_id' => $poll1->id, 'label' => 'X']);

        $poll2 = Poll::create(['question' => 'Second?']);
        PollOption::create(['poll_id' => $poll2->id, 'label' => 'Y']);

        $result = $service->listPolls();

        $this->assertEquals(2, $result->count());
        $this->assertTrue($result->pluck('id')->contains($poll1->id));
        $this->assertTrue($result->pluck('id')->contains($poll2->id));
    }

    public function test_list_polls_includes_vote_counts()
    {
        $service = new PollService();

        $poll = Poll::create(['question' => 'Count test?']);
        $opt1 = PollOption::create(['poll_id' => $poll->id, 'label' => 'A']);
        $opt2 = PollOption::create(['poll_id' => $poll->id, 'label' => 'B']);

        Vote::create(['poll_id' => $poll->id, 'poll_option_id' => $opt1->id, 'ip_address' => '127.0.0.1']);
        Vote::create(['poll_id' => $poll->id, 'poll_option_id' => $opt2->id, 'ip_address' => '127.0.0.2']);
        Vote::create(['poll_id' => $poll->id, 'poll_option_id' => $opt2->id, 'ip_address' => '127.0.0.3']);

        $opt1->increment('votes_count');
        $opt2->increment('votes_count', 2);

        $result = $service->listPolls();

        $this->assertEquals(1, $result->count());
        $this->assertGreaterThan(0, $result->first()->votes_count);
    }
}
