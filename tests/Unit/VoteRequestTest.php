<?php

namespace Tests\Unit;

use App\Http\Requests\VoteRequest;
use PHPUnit\Framework\TestCase;

final class VoteRequestTest extends TestCase
{
    public function test_vote_request_has_required_rules(): void
    {
        $request = new VoteRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('option_id', $rules);
        $this->assertStringContainsString('required', $rules['option_id']);
        $this->assertStringContainsString('exists:poll_options,id', $rules['option_id']);
    }

    public function test_vote_request_is_authorized(): void
    {
        $request = new VoteRequest();
        $this->assertTrue($request->authorize());
    }
}
