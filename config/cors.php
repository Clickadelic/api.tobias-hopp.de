<?php

return [
	'paths' => ['api/*', 'sanctum/csrf-cookie'],
	'allowed_methods' => ['*'],
	'allowed_origins' => ['https://www.tobias-hopp.de', 'https://*.tobias-hopp.de', 'http://localhost:*',],
	'allowed_origins_patterns' => [],
	'allowed_headers' => ['*'],
	'exposed_headers' => [],
	'max_age' => 0,
	'supports_credentials' => true,
];
