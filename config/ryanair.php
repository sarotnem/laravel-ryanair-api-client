<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ryanair API Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Ryanair API
    |
    */
    'api_base' => env('RYANAIR_API_BASE', 'http://apigateway.ryanair.com/pub/v1'),
    'api_key' => env('RYANAIR_API_KEY', 'YOUR_API_KEY'),
    'cache_expire' => env('RYANAIR_CACHE_EXPIRE', 3600)
];
