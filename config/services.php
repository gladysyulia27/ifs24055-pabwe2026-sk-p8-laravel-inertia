<?php

return [
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'organization' => env('GEMINI_ORGANIZATION'),
        'project' => env('GEMINI_PROJECT'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
    ],
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'project' => env('OPENAI_PROJECT'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],
];
