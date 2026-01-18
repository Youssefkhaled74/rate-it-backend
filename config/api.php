<?php

return [
    'pagination' => [
        'default_limit' => env('API_DEFAULT_LIMIT', 20),
        'max_limit' => env('API_MAX_LIMIT', 100),
    ],
];
