<?php

namespace App\Services;

use App\Events\VoteCast;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use App\Models\User;
use DomainException;
use Illuminate\Pagination\LengthAwarePaginator;

final class PollService
{
    public function getPollWithOptions(Poll $poll): Poll
    {
        return $poll->load('options');
    }

    public function listPolls(int $perPage = 10): LengthAwarePaginator
    {
        return Poll::withCount('votes')->with('options')->orderByDesc('created_at')->paginate($perPage);
    }

    public function submitVote(Poll $poll, int $optionId, ?User $user, string $ip): Vote
    {
        $option = PollOption::where('id', $optionId)->where('poll_id', $poll->id)->firstOrFail();

        $existing = Vote::where('poll_id', $poll->id)
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(!$user, fn($q) => $q->where('ip_address', $ip))
            ->first();

        if ($existing) {
            throw new DomainException('You have already voted.');
        }

        $vote = Vote::create([
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'user_id' => $user?->id,
            'ip_address' => $ip,
        ]);

        $option->increment('votes_count');

        event(new VoteCast($poll, $option));

        return $vote;
    }
}
