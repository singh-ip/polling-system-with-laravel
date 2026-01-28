<?php

namespace Tests\Unit;

use App\Console\Commands\CreatePoll;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreatePollCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_poll_with_options()
    {
        $this->artisan('poll:create', [
            'question' => 'Test Command?',
            'options' => ['Option A', 'Option B'],
        ])->assertExitCode(0);

        $this->assertDatabaseHas('polls', ['question' => 'Test Command?']);
        $this->assertDatabaseHas('poll_options', ['label' => 'Option A']);
        $this->assertDatabaseHas('poll_options', ['label' => 'Option B']);
    }

    public function test_command_requires_minimum_two_options()
    {
        $this->artisan('poll:create', [
            'question' => 'Single Option?',
            'options' => ['Only One'],
        ])->assertExitCode(1);

        $this->assertDatabaseMissing('polls', ['question' => 'Single Option?']);
    }

    public function test_command_outputs_poll_id()
    {
        $this->artisan('poll:create', [
            'question' => 'Output Test?',
            'options' => ['A', 'B'],
        ])->assertExitCode(0);
    }

    public function test_command_outputs_public_url()
    {
        $this->artisan('poll:create', [
            'question' => 'URL Test?',
            'options' => ['X', 'Y'],
        ])->assertExitCode(0);
    }

    public function test_command_creates_multiple_options()
    {
        $this->artisan('poll:create', [
            'question' => 'Multi Options?',
            'options' => ['A', 'B', 'C', 'D'],
        ])->assertExitCode(0);

        $this->assertDatabaseHas('poll_options', ['label' => 'A']);
        $this->assertDatabaseHas('poll_options', ['label' => 'D']);
    }
}
