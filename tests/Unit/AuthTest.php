<?php

namespace Tests\Unit;

use App\Http\Services\AuthService;
use App\Http\Services\UserServices;
use App\Http\Services\VerificationCodeServices;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    private $authService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService(new VerificationCodeServices(), new UserServices());
    }

    public function test_register_user_duplicate_name()
    {
        $existUser = User::factory(1)->create();

        
    }
}
