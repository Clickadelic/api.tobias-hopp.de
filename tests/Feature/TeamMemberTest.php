<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function memberTeam(User $owner): Team
{
	$team = Team::create(['name' => 'Crew']);
	$team->users()->attach($owner->getKey(), ['role' => Team::ROLE_OWNER]);

	return $team;
}

test('regular members cannot promote members', function () {
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$team = memberTeam($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);

	$this->actingAs($member, 'sanctum')
		->patchJson("/api/v1/teams/{$team->id}/members/{$member->id}", ['role' => 'owner'])
		->assertForbidden();
});

test('owners can change roles and remove members', function () {
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$team = memberTeam($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);

	$this->actingAs($owner, 'sanctum')
		->patchJson("/api/v1/teams/{$team->id}/members/{$member->id}", ['role' => 'owner'])
		->assertOk();
	expect($team->isOwnedBy($member))->toBeTrue();

	$this->actingAs($owner, 'sanctum')
		->deleteJson("/api/v1/teams/{$team->id}/members/{$member->id}")
		->assertNoContent();
	expect($team->hasMember($member))->toBeFalse();
});

test('members can leave but the last owner cannot', function () {
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$team = memberTeam($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);

	$this->actingAs($member, 'sanctum')
		->deleteJson("/api/v1/teams/{$team->id}/members/{$member->id}")
		->assertNoContent();

	$this->actingAs($owner, 'sanctum')
		->deleteJson("/api/v1/teams/{$team->id}/members/{$owner->id}")
		->assertUnprocessable();
	$this->actingAs($owner, 'sanctum')
		->patchJson("/api/v1/teams/{$team->id}/members/{$owner->id}", ['role' => 'member'])
		->assertUnprocessable();
});

test('members cannot remove other members', function () {
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$team = memberTeam($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);

	$this->actingAs($member, 'sanctum')
		->deleteJson("/api/v1/teams/{$team->id}/members/{$owner->id}")
		->assertForbidden();
});
