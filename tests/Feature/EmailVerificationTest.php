<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('sends a verification notification when a user registers', function () {
	Notification::fake();

	$response = $this->postJson('/api/auth/register', [
		'name' => 'Jane Doe',
		'email' => 'jane@example.com',
		'password' => 'password',
		'password_confirmation' => 'password',
	]);

	$response->assertCreated()
		->assertJsonPath('user.email_verified_at', null);

	Notification::assertSentTo(User::first(), VerifyEmail::class);
});

it('requires verified email addresses for protected api routes', function () {
	$user = User::factory()->unverified()->create([
		'name' => 'Jane Doe',
		'email' => 'jane@example.com',
		'password' => 'password',
	]);
	$token = $user->createToken('test-token')->plainTextToken;

	$this->withToken($token)
		->getJson('/api/v1/me')
		->assertForbidden();

	$verificationUrl = URL::temporarySignedRoute(
		'verification.verify',
		now()->addMinutes(10),
		[
			'id' => $user->getKey(),
			'hash' => sha1($user->getEmailForVerification()),
		],
	);

	$this->getJson($verificationUrl)
		->assertOk()
		->assertJsonPath('email_verified', true);

	expect(User::find($user->getKey())->hasVerifiedEmail())->toBeTrue();

	auth()->forgetGuards();

	$this->withToken($token)
		->getJson('/api/v1/me')
		->assertOk();
});

it('can resend a verification notification', function () {
	Notification::fake();

	$user = User::factory()->unverified()->create([
		'name' => 'Jane Doe',
		'email' => 'jane@example.com',
		'password' => 'password',
	]);
	$token = $user->createToken('test-token')->plainTextToken;

	$this->withToken($token)
		->postJson('/api/auth/email/verification-notification')
		->assertOk()
		->assertJsonPath('message', 'Verification email sent.');

	Notification::assertSentTo($user, VerifyEmail::class);
});

it('sends a password reset notification when requested', function () {
	Notification::fake();

	$user = User::factory()->create(['email' => 'jane@example.com']);

	$this->postJson('/api/auth/forgot-password', ['email' => $user->email])
		->assertOk()
		->assertJsonPath('message', 'If an account exists for that email address, a password reset link has been sent.');

	Notification::assertSentTo($user, ResetPassword::class);
});
