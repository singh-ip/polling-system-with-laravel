<?php

namespace Tests\Unit;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollOptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_votes_count_can_be_incremented()
    {
        $poll = Poll::create(['question' => 'Unit test?']);
        $option = PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes']);

        $this->assertEquals(0, $option->votes_count);

        $option->increment('votes_count');
        $this->assertEquals(1, $option->fresh()->votes_count);
    }
}
