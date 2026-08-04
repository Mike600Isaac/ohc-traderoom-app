<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\OHCVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/verify-email')
            ->assertOk()
            ->assertSee('Verify your email address')
            ->assertSee($user->email);
    }

    public function test_unverified_user_cannot_open_member_or_admin_workspaces(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route('verification.notice', absolute: false));
        $this->actingAs($user)->get('/learn')
            ->assertRedirect(route('verification.notice', absolute: false));
        $this->actingAs($user)->get('/admin')
            ->assertRedirect(route('verification.notice', absolute: false));
    }

    public function test_user_can_request_a_fresh_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post('/email/verification-notification')
            ->assertRedirect()
            ->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, OHCVerifyEmail::class);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();
        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
        $response->assertSessionHas('status', 'Email verified successfully. Your member workspace is now available.');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl)->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
