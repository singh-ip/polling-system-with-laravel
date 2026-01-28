<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PollDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_poll_page()
    {
        $poll = Poll::create(['question' => 'Display test?']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'Yes']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'No']);

        $response = $this->get('/polls/' . $poll->id);

        $response->assertStatus(200);
        $response->assertSee('Display test?');
        $response->assertSee('Yes');
        $response->assertSee('No');
    }

    public function test_poll_page_displays_vote_counts()
    {
        $poll = Poll::create(['question' => 'Count display?']);
        $opt1 = PollOption::create(['poll_id' => $poll->id, 'label' => 'A', 'votes_count' => 5]);
        $opt2 = PollOption::create(['poll_id' => $poll->id, 'label' => 'B', 'votes_count' => 3]);

        $response = $this->get('/polls/' . $poll->id);

        $response->assertStatus(200);
        $response->assertSee('5');
        $response->assertSee('3');
    }

    public function test_poll_page_shows_shareable_link()
    {
        $poll = Poll::create(['question' => 'Share test?']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'Y']);

        $response = $this->get('/polls/' . $poll->id);

        $response->assertStatus(200);
        $response->assertSee('/polls/' . $poll->id);
    }

    public function test_poll_page_returns_404_for_nonexistent_poll()
    {
        $response = $this->get('/polls/9999');
        $response->assertStatus(404);
    }

    public function test_poll_page_includes_vote_buttons()
    {
        $poll = Poll::create(['question' => 'Button test?']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'Option']);

        $response = $this->get('/polls/' . $poll->id);

        $response->assertStatus(200);
        $response->assertSee('Vote');
    }
}
