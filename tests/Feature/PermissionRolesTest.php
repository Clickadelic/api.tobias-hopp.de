<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('new registrations receive the user role', function () {
	Role::create(['name' => 'user', 'guard_name' => 'web']);

	$response = $this->postJson('/api/auth/register', [
		'name' => 'Regular User',
		'email' => 'regular@example.com',
		'password' => 'password123',
		'password_confirmation' => 'password123',
	]);

	$response->assertCreated()->assertJsonPath('user.roles', ['user']);

	expect(User::where('email', 'regular@example.com')->first())
		->not->toBeNull()
		->and(User::where('email', 'regular@example.com')->first()->hasRole('user'))->toBeTrue();
});

test('an administrator can be assigned the admin role', function () {
	$adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
	$user = User::create([
		'name' => 'Personal Admin',
		'email' => 'admin@example.com',
		'password' => Hash::make('password123'),
	]);

	$user->assignRole($adminRole);

	expect($user->hasRole('admin'))->toBeTrue()
		->and($user->hasRole('user'))->toBeFalse();
});

test('login includes the users roles', function () {
	Role::create(['name' => 'admin', 'guard_name' => 'web']);
	$user = User::factory()->create([
		'email' => 'admin@example.com',
		'password' => 'password123',
	]);
	$user->assignRole('admin');

	$this->postJson('/api/auth/login', [
		'email' => 'admin@example.com',
		'password' => 'password123',
	])->assertOk()->assertJsonPath('user.roles', ['admin']);
});
