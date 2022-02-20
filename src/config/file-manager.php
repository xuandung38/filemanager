<?php
return [
    'directory' => 'public/files',
    'anonymous_folder' => 'anonymous',
    'user_storage_limit' => 1024000,
    'anonymous_upload' => false,
    'mimes' => [
        'image/jpeg',
        'image/gif',
        'image/png',
        'image/svg',
        'image/webp',
        'video/webm',
        'video/mp4',
    ],
    'guards' => [
        'user',
        'admin'
    ]
];
