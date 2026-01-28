<?php

namespace App\Console\Commands;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Console\Command;

final class CreatePoll extends Command
{
    protected $signature = 'poll:create {question} {options*}';
    protected $description = 'Create a poll (question and at least two options)';

    public function handle()
    {
        $question = $this->argument('question');
        $options = $this->argument('options');

        if (count($options) < 2) {
            $this->error('Please provide at least two options');
            return 1;
        }

        $poll = Poll::create(['question' => $question]);

        foreach ($options as $opt) {
            PollOption::create(['poll_id' => $poll->id, 'label' => $opt]);
        }

        $this->info('Poll created: ' . $poll->id);
        $this->info('Public URL: ' . url('/polls/' . $poll->id));

        return 0;
    }
}
