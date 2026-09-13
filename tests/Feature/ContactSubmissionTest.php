<?php

use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

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
