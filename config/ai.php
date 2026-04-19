<?php

return [
    'driver' => env('AI_DRIVER', 'mock'),

    'ollama' => [
        'host' => env('OLLAMA_HOST', 'http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL', 'aisingapore/Gemma-SEA-LION-v4-4B-VL'),
        'timeout' => (int) env('OLLAMA_TIMEOUT', 20),
    ],
];
