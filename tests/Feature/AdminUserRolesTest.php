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
