<?php 

return [
	'mail' => [
		'mail_gun' => [
			'API_KEY' => getenv('MAILGUN_API_KEY'),
			'DOMAIN' => getenv('MAILGUN_DOMAIN'),
			'FROM_ADDRESS' => getenv('MAILGUN_FROM_ADDRESS'),
		],		
		'send_grid' => [
			
		]
	]
];