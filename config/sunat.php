<?php

return [
    'api_url' => env(
        'SUNAT_API_URL',
        in_array(env('APP_ENV', 'production'), ['local', 'development'])
            ? 'https://magus-qa.com/api-sunat-laravel'
            : 'https://magus-qa.com/api-sunat-laravel'
    ),
];
