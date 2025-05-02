<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | This value is the base URL for the API endpoints. You can set different
    | values for different environments.
    |
    */
    'base_url' => env('API_URL', config('app.url')),
    
    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | Define all API endpoints here for centralized management
    |
    */
    'endpoints' => [
        'login' => '/api/auth/login',
        'register' => '/api/auth/register',
        'logout' => '/api/auth/logout',
        'user' => '/api/auth/user',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | API Timeout
    |--------------------------------------------------------------------------
    |
    | Define API request timeout in seconds
    |
    */
    'timeout' => env('API_TIMEOUT', 30),
];