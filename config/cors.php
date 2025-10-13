<?php

return [
    'paths' => ['api/*', 'admin/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173'], // 前端地址
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
