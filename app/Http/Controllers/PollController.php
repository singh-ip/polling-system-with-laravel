<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\VoteRequest;
use App\Models\Poll;
use App\Services\PollService;
use DomainException;

final class PollController extends Controller
{
    private PollService $service;

    public function __construct(PollService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $polls = $this->service->listPolls();
        return view('poll.index', compact('polls'));
    }
}
