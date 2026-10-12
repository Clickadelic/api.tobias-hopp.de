<?php

use App\Mail\TeamInvitationMail;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function invitableTeam(User $owner): Team
{
	$team = Team::create(['name' => 'Invite Co']);
	$team->users()->attach($owner->getKey(), ['role' => Team::ROLE_OWNER]);

	return $team;
}

function sendInvite(User $owner, Team $team, string $email): string
{
	test()->actingAs($owner, 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/invitations", ['email' => $email])
		->assertCreated();

	$token = null;
	Mail::assertSent(TeamInvitationMail::class, function ($mail) use (&$token) {
		$token = $mail->plainToken;

		return true;
	});

	return $token;
}

test('owners can invite by email and the token is stored hashed', function () {
	Mail::fake();
	$owner = User::factory()->create();
	$team = invitableTeam($owner);

	$token = sendInvite($owner, $team, 'New@Example.com');

	$invitation = TeamInvitation::first();
	expect($invitation->email)->toBe('new@example.com')
		->and($invitation->token)->not->toBe($token)
		->and($invitation->token)->toBe(hash('sha256', $token));
});

test('regular members and outsiders cannot invite', function () {
	Mail::fake();
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$team = invitableTeam($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);

	$this->actingAs($member, 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/invitations", ['email' => 'x@example.com'])
		->assertForbidden();
	$this->actingAs(User::factory()->create(), 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/invitations", ['email' => 'x@example.com'])
		->assertForbidden();

	Mail::assertNothingSent();
});

test('inviting an existing member is rejected', function () {
	Mail::fake();
	$owner = User::factory()->create();
	$team = invitableTeam($owner);

	$this->actingAs($owner, 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/invitations", ['email' => $owner->email])
		->assertUnprocessable();
});

test('the invited user can accept and joins with the invited role', function () {
	Mail::fake();
	$owner = User::factory()->create();
	$invitee = User::factory()->create();
	$team = invitableTeam($owner);
	$token = sendInvite($owner, $team, $invitee->email);

	$this->actingAs($invitee, 'sanctum')
		->postJson('/api/v1/team-invitations/accept', ['token' => $token])
		->assertOk();

	expect($team->hasMember($invitee))->toBeTrue()
		->and(TeamInvitation::count())->toBe(0);
});

test('a different user cannot accept someone elses invitation', function () {
	Mail::fake();
	$owner = User::factory()->create();
	$team = invitableTeam($owner);
	$token = sendInvite($owner, $team, 'someone@example.com');
	$thief = User::factory()->create();

	$this->actingAs($thief, 'sanctum')
		->postJson('/api/v1/team-invitations/accept', ['token' => $token])
		->assertUnprocessable();

	expect($team->hasMember($thief))->toBeFalse();
});

test('expired invitations cannot be accepted', function () {
	Mail::fake();
	$owner = User::factory()->create();
	$invitee = User::factory()->create();
	$team = invitableTeam($owner);
	$token = sendInvite($owner, $team, $invitee->email);
	TeamInvitation::query()->update(['expires_at' => now()->subMinute()]);

	$this->actingAs($invitee, 'sanctum')
		->postJson('/api/v1/team-invitations/accept', ['token' => $token])
		->assertUnprocessable();
});

test('owners can revoke pending invitations', function () {
	Mail::fake();
	$owner = User::factory()->create();
	$team = invitableTeam($owner);
	sendInvite($owner, $team, 'x@example.com');
	$invitation = TeamInvitation::first();

	$this->actingAs($owner, 'sanctum')
		->deleteJson("/api/v1/teams/{$team->id}/invitations/{$invitation->id}")
		->assertNoContent();

	expect(TeamInvitation::count())->toBe(0);
});

test('owners can transfer ownership to another member', function () {
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$team = invitableTeam($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);

	$this->actingAs($owner, 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/transfer-ownership", ['user_id' => $member->id])
		->assertOk();

	expect($team->isOwnedBy($member))->toBeTrue()
		->and($team->isOwnedBy($owner))->toBeFalse()
		->and($team->hasMember($owner))->toBeTrue();
});

test('ownership can only go to existing members and only by an owner', function () {
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$outsider = User::factory()->create();
	$team = invitableTeam($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);

	$this->actingAs($owner, 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/transfer-ownership", ['user_id' => $outsider->id])
		->assertUnprocessable();
	$this->actingAs($owner, 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/transfer-ownership", ['user_id' => $owner->id])
		->assertUnprocessable();
	$this->actingAs($member, 'sanctum')
		->postJson("/api/v1/teams/{$team->id}/transfer-ownership", ['user_id' => $member->id])
		->assertForbidden();
});
