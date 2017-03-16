<?php

return [
    'app_url' => getenv('APP_URL'),
    'app_name' => getenv('APP_NAME'),
    
    'settings' => [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
    ],
];
