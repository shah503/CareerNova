<?php

return [
    'openai_key' => env('OPENAI_API_KEY'),
    'gemini_key' => env('GEMINI_API_KEY'),

    'generation' => [
        'model' => 'gpt-3.5-turbo',
        'max_tokens' => 2000,
        'temperature' => 0.7,
    ],

    'validation' => [
        'check_duplicates' => true,
        'check_structure' => true,
        'check_accuracy' => true,
        'check_difficulty' => true,
    ]
];