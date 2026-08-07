<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\OHCVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/profile')->assertOk();
    }

    public function test_changing_email_revokes_verification_and_sends_a_new_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'timezone' => 'Africa/Lagos',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('verification.notice', absolute: false));

        $user->refresh();
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, OHCVerifyEmail::class);
    }

    public function test_verification_is_unchanged_when_email_is_unchanged(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $user->email,
            'timezone' => 'Africa/Lagos',
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
        Notification::assertNothingSent();
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete('/profile', ['password' => 'password'])
            ->assertSessionHasNoErrors()->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_is_required_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')
            ->delete('/profile', ['password' => 'wrong-password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
