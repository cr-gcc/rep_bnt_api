<?php

return [
	'paths' => ['api/*', 'login', 'logout', 'me'],
	'allowed_methods' => ['*'],
	'allowed_origins' => [
		'http://192.168.31.10',
		'http://localhost:5173',
	],
	'allowed_origins_patterns' => [],
	'allowed_headers' => ['*'],
	'exposed_headers' => [],
	'max_age' => 0,
	'supports_credentials' => true,
];
