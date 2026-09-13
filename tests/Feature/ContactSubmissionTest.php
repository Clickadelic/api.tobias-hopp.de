<?php

use Illuminate\Support\Facades\Config;

use function Pest\Laravel\getJson;

beforeEach(function () {
	Config::set('app.debug', true);
});

it('rejects unauthenticated requests to the contact submissions index', function () {
	getJson('/api/contact-submissions')
		->assertUnauthorized();
});
