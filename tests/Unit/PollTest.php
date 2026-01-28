<?php

namespace Tests\Unit;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PollTest extends TestCase
{
    use RefreshDatabase;

    public function test_poll_can_be_created()
    {
        $poll = Poll::create(['question' => 'Test poll?']);
        $this->assertDatabaseHas('polls', ['question' => 'Test poll?']);
    }

    public function test_poll_has_options_relation()
    {
        $poll = Poll::create(['question' => 'Relation test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Option A']);

        $this->assertTrue($poll->options->contains($option));
    }

    public function test_poll_can_load_options_with_votes()
    {
        $poll = Poll::create(['question' => 'Votes test?']);
        $opt1 = PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes', 'votes_count' => 5]);
        $opt2 = PollOption::create(['poll_id' => $poll->id, 'label' => 'No', 'votes_count' => 3]);

        $this->assertEquals(2, $poll->options->count());
        $this->assertEquals(5, $poll->options->first()->votes_count);
    }

    public function test_poll_has_votes_relation()
    {
        $poll = Poll::create(['question' => 'Vote relation?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'X']);

        $poll->votes()->create(['poll_option_id' => $option->id, 'ip_address' => '127.0.0.1']);

        $this->assertEquals(1, $poll->votes->count());
    }

    public function test_poll_has_timestamps()
    {
        $poll = Poll::create(['question' => 'Timestamp test?']);
        $this->assertNotNull($poll->created_at);
        $this->assertNotNull($poll->updated_at);
    }
}
