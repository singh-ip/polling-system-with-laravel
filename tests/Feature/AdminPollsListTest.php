<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminPollsListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_view_polls_listing()
    {
        $poll1 = Poll::create(['question' => 'First poll?']);
        $poll2 = Poll::create(['question' => 'Second poll?']);

        $response = $this->actingAs($this->user)->get('/polls');

        $response->assertStatus(200);
        $response->assertSee('First poll?');
        $response->assertSee('Second poll?');
    }

    public function test_admin_listing_shows_vote_counts()
    {
        $poll = Poll::create(['question' => 'Count poll?']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'A', 'votes_count' => 10]);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'B', 'votes_count' => 5]);

        $response = $this->actingAs($this->user)->get('/polls');

        $response->assertStatus(200);
        $response->assertSee('votes:');
    }

    public function test_admin_listing_links_to_poll_page()
    {
        $poll = Poll::create(['question' => 'Link test?']);
        PollOption::create(['poll_id' => $poll->id, 'label' => 'X']);

        $response = $this->actingAs($this->user)->get('/polls');

        $response->assertStatus(200);
        $response->assertSee('/polls/' . $poll->id);
    }

    public function test_admin_listing_shows_empty_when_no_polls()
    {
        $response = $this->actingAs($this->user)->get('/polls');

        $response->assertStatus(200);
    }
}
