<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\OHCVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_new_users_are_signed_in_and_sent_a_verification_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'Valid1!x',
            'password_confirmation' => 'Valid1!x',
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasVerifiedEmail());
        $response->assertRedirect(route('verification.notice', absolute: false));
        Notification::assertSentTo($user, OHCVerifyEmail::class);
    }
}
