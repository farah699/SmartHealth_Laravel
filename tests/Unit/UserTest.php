<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_user_has_required_attributes(): void
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
    }

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'test@smarthealth.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => 'test@smarthealth.com']);
    }
}