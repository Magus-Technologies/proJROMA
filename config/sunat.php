<?php

return [
    'api_url' => env(
        'SUNAT_API_URL',
        in_array(env('APP_ENV', 'production'), ['local', 'development'])
            ? 'http://api-sunat-laravel.test'
            : 'https://magus-qa.com/api-sunat-laravel'
    ),
];
