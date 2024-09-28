<?php

return [
    'enabled' => env('LOG_VIEWER_ENABLED', true),
    'route_domain' => null,
    'route_path' => 'logs',
    'back_to_system_url' => config('app.url', null),
    'back_to_system_label' => null,
    'timezone' => null,
    'middleware' => ['web'],
    'api_middleware' => [],
    'hosts' => ['local' => ['name' => ucfirst(env('APP_ENV', 'local'))]],
    'include_files' => ['*.log', '**/*.log'],
    'exclude_files' => [],
    'hide_unknown_files' => true,
    'shorter_stack_trace_excludes' => ['/vendor/symfony/', '/vendor/laravel/framework/',   '/vendor/barryvdh/laravel-debugbar/',],
    'cache_driver' => env('LOG_VIEWER_CACHE_DRIVER', null),
    'lazy_scan_chunk_size_in_mb' => 50,
    'strip_extracted_context' => true
];
