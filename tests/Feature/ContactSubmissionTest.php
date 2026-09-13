<?php

use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
	Config::set('app.debug', true);
});

it('rejects unauthenticated requests to the contact submissions index', function () {
	getJson('/api/contact-submissions')
		->assertUnauthorized();
});

it('saves a contact submission request', function () {
	Mail::fake();

	$postData = [
		'name' => 'John Doe',
		'phone' => '1234567890',
		'email' => 'TbI9o@example.com',
		'subject' => 'Test Subject',
		'message' => 'Hello, this is a test message.',
	];

	postJson('/api/contact-submissions', $postData)
		->assertCreated();

	expect(ContactSubmission::query()->where('email', $postData['email'])->exists())->toBeTrue();
});

it('rejects regular users from viewing contact submissions', function () {
	Role::create(['name' => 'user', 'guard_name' => 'web']);
	$regularUser = User::factory()->create();
	$regularUser->assignRole('user');

	$this->actingAs($regularUser, 'sanctum')
		->getJson('/api/contact-submissions')
		->assertForbidden();
});

it('allows admins to view contact submissions', function () {
	Role::create(['name' => 'admin', 'guard_name' => 'web']);
	$admin = User::factory()->create();
	$admin->assignRole('admin');

	$this->actingAs($admin, 'sanctum')
		->getJson('/api/contact-submissions')
		->assertOk();
});
