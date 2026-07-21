<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_home_can_be_rendered(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_application_does_not_expose_a_public_profile_editor(): void
    {
        $this->get('/profile')->assertNotFound();
    }
}
