<?php

return [
    'base_dir' => env('UPLOADS_DIR', 'uploads'),
    'allowed_mimes' => ['jpg','jpeg','png','webp'],
    'max_size_kb' => env('UPLOAD_MAX_KB', 5120),
    'contexts' => [
        'reviews' => ['max_files' => 3],
        'avatars' => ['max_files' => 1],
    ],
];
