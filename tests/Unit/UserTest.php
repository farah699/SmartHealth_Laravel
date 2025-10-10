<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@smarthealth.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@smarthealth.com',
            'name' => 'Test User',
        ]);
    }

    public function test_user_has_required_attributes(): void
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
    }
}