<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API Key and organization. This will be
    | used to authenticate with the Gemini API - you can find your API key
    | on Google AI Studio, at https://aistudio.google.com/app/apikey.
    */

    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Gemini Model
    |--------------------------------------------------------------------------
    |
    | Use a current, free-tier friendly text model by default. Keep this in
    | config so cached Laravel config still reads the expected model value.
    |
    */

    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash-lite'),

    /*
    |--------------------------------------------------------------------------
    | Generation Defaults
    |--------------------------------------------------------------------------
    */

    'temperature' => (float) env('GEMINI_TEMPERATURE', 0.3),
    'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 2048),

    /*
    |--------------------------------------------------------------------------
    | Gemini Base URL
    |--------------------------------------------------------------------------
    |
    | If you need a specific base URL for the Gemini API, you can provide it here.
    | Otherwise, leave empty to use the default value.
    */
    'base_url' => env('GEMINI_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 60),
];
