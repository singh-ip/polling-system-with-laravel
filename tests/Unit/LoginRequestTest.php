<?php

namespace Tests\Unit;

use App\Http\Requests\LoginRequest;
use Tests\TestCase;

final class LoginRequestTest extends TestCase
{
    public function test_login_request_validates_required_email()
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertStringContainsString('required', $rules['email']);
    }

    public function test_login_request_validates_email_format()
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('email', $rules['email']);
    }

    public function test_login_request_validates_required_password()
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('password', $rules);
        $this->assertStringContainsString('required', $rules['password']);
    }

    public function test_login_request_validates_password_minimum_length()
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('min:6', $rules['password']);
    }

    public function test_login_request_authorizes_all_users()
    {
        $request = new LoginRequest();

        $this->assertTrue($request->authorize());
    }
}
