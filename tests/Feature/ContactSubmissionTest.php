<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Models\ContactSubmission;
use App\Mail\ContactSubmissionMail;
use function Pest\Laravel\get;

beforeEach(function () {
	Config::set('app.debug', true);
});

it('should save a contact submission request', function () {
	Mail::to('mail@tobias-hopp.de')->send(new ContactSubmissionMail());

	$postData = [
		'name' => 'John Doe',
		'phone' => '1234567890',
		'email' => 'TbI9o@example.com',
		'subject' => 'Test Subject',
		'message' => 'Hello, this is a test message.',
	];

	get('/api/contact-submissions', $postData)
		->assertStatus(200);
});
