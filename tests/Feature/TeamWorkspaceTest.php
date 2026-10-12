<?php

use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function teamFor(User $user, string $role = Team::ROLE_OWNER): Team
{
	$team = Team::create(['name' => 'Acme']);
	$team->users()->attach($user->getKey(), ['role' => $role]);

	return $team;
}

test('creating a team makes the creator its owner and generates a slug', function () {
	$user = User::factory()->create();

	$this->actingAs($user, 'sanctum')
		->postJson('/api/v1/teams', ['name' => 'My Team'])
		->assertCreated()
		->assertJsonPath('slug', 'my-team');

	expect($user->teams()->first()->pivot->role)->toBe(Team::ROLE_OWNER);
});

test('members can create workspaces in their team with an auto slug', function () {
	$user = User::factory()->create();
	$team = teamFor($user, Team::ROLE_MEMBER);

	$this->actingAs($user, 'sanctum')
		->postJson('/api/v1/workspaces', ['team_id' => $team->id, 'name' => 'Product Launch'])
		->assertCreated()
		->assertJsonPath('slug', 'product-launch');
});

test('users cannot create workspaces in foreign teams', function () {
	$team = teamFor(User::factory()->create());

	$this->actingAs(User::factory()->create(), 'sanctum')
		->postJson('/api/v1/workspaces', ['team_id' => $team->id, 'name' => 'Nope'])
		->assertForbidden();
});

test('workspace listing only includes workspaces of own teams', function () {
	$user = User::factory()->create();
	$mine = Workspace::create(['team_id' => teamFor($user)->id, 'name' => 'Mine']);
	Workspace::create(['team_id' => teamFor(User::factory()->create())->id, 'name' => 'Theirs']);

	$this->actingAs($user, 'sanctum')
		->getJson('/api/v1/workspaces')
		->assertOk()
		->assertJsonCount(1, 'data')
		->assertJsonPath('data.0.uuid', $mine->uuid);
});

test('non-members cannot view, update or delete a workspace', function () {
	$workspace = Workspace::create(['team_id' => teamFor(User::factory()->create())->id, 'name' => 'Secret']);
	$outsider = User::factory()->create();

	$this->actingAs($outsider, 'sanctum')->getJson("/api/v1/workspaces/{$workspace->uuid}")->assertForbidden();
	$this->actingAs($outsider, 'sanctum')->putJson("/api/v1/workspaces/{$workspace->uuid}", ['name' => 'x'])->assertForbidden();
	$this->actingAs($outsider, 'sanctum')->deleteJson("/api/v1/workspaces/{$workspace->uuid}")->assertForbidden();
});

test('only owners can delete workspaces', function () {
	$owner = User::factory()->create();
	$member = User::factory()->create();
	$team = teamFor($owner);
	$team->users()->attach($member->getKey(), ['role' => Team::ROLE_MEMBER]);
	$workspace = Workspace::create(['team_id' => $team->id, 'name' => 'Board space']);

	$this->actingAs($member, 'sanctum')->deleteJson("/api/v1/workspaces/{$workspace->uuid}")->assertForbidden();
	$this->actingAs($owner, 'sanctum')->deleteJson("/api/v1/workspaces/{$workspace->uuid}")->assertNoContent();
});
