<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class CategoriesController extends TestCase
{
    use RefreshDatabase;
    private $categoriesController;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        $this->categoriesController = app(CategoriesController::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
