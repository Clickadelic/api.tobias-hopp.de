<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
	Role::create(['name' => 'user', 'guard_name' => 'web']);
	Role::create(['name' => 'admin', 'guard_name' => 'web']);
});

test('admins can list users with their roles', function () {
	$admin = User::factory()->create(['name' => 'Admin User']);
	$admin->assignRole('admin');
	$user = User::factory()->create(['name' => 'Regular User']);
	$user->assignRole('user');

	$response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/users');

	$response->assertOk()
		->assertJsonPath('users.0.roles', ['admin'])
		->assertJsonPath('users.1.roles', ['user']);
});

test('admins can delegate the admin role', function () {
	$admin = User::factory()->create();
	$admin->assignRole('admin');
	$user = User::factory()->create();
	$user->assignRole('user');

	$response = $this->actingAs($admin, 'sanctum')
		->patchJson("/api/v1/users/{$user->id}/role", ['role' => 'admin']);

	$response->assertOk()->assertJsonPath('data.roles', ['admin']);
	expect($user->refresh()->hasRole('admin'))->toBeTrue();
});

test('regular users cannot delegate roles', function () {
	$user = User::factory()->create();
	$user->assignRole('user');
	$otherUser = User::factory()->create();
	$otherUser->assignRole('user');

	$this->actingAs($user, 'sanctum')
		->patchJson("/api/v1/users/{$otherUser->id}/role", ['role' => 'admin'])
		->assertForbidden();
});

test('admins cannot remove their own admin role', function () {
	$admin = User::factory()->create();
	$admin->assignRole('admin');

	$this->actingAs($admin, 'sanctum')
		->patchJson("/api/v1/users/{$admin->id}/role", ['role' => 'user'])
		->assertUnprocessable();
});

test('admins can delete other users', function () {
	$admin = User::factory()->create();
	$admin->assignRole('admin');
	$user = User::factory()->create();
	$user->assignRole('user');

	$this->actingAs($admin, 'sanctum')
		->deleteJson("/api/v1/users/{$user->id}")
		->assertOk();

	$this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('admins cannot delete their own account', function () {
	$admin = User::factory()->create();
	$admin->assignRole('admin');

	$this->actingAs($admin, 'sanctum')
		->deleteJson("/api/v1/users/{$admin->id}")
		->assertUnprocessable();

	$this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('regular users cannot delete users', function () {
	$user = User::factory()->create();
	$user->assignRole('user');
	$other = User::factory()->create();
	$other->assignRole('user');

	$this->actingAs($user, 'sanctum')
		->deleteJson("/api/v1/users/{$other->id}")
		->assertForbidden();

	$this->assertDatabaseHas('users', ['id' => $other->id]);
});

test('regular users cannot list users', function () {
	$user = User::factory()->create();
	$user->assignRole('user');

	$this->actingAs($user, 'sanctum')->getJson('/api/v1/users')->assertForbidden();
});

test('guests cannot use the admin user endpoints', function () {
	$user = User::factory()->create();

	$this->getJson('/api/v1/users')->assertUnauthorized();
	$this->patchJson("/api/v1/users/{$user->id}/role", ['role' => 'admin'])->assertUnauthorized();
	$this->deleteJson("/api/v1/users/{$user->id}")->assertUnauthorized();
	$this->assertDatabaseHas('users', ['id' => $user->id]);
});
