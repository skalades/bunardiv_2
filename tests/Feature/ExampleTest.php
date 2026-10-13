<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');

        $user = User::first() ?? User::factory()->create();
        $authResponse = $this->actingAs($user)->get('/');
        $authResponse->assertStatus(200);
    }
}
