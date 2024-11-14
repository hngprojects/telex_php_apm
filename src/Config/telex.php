<?php

/**
 * Telex Laravel SDK configuration file.
 *
*/

return [
    'providers' => [
        TelexAPM\APMServiceProvider::class,
    ],

    'aliases' => [
        'APM' => TelexAPM\Facades\APM::class,
    ],

    // Basic Configuration
    'enabled' => env('APM_ENABLED', true),
    'webhook_url' => env('APM_WEBHOOK_URL'),
    'environment' => env('APM_ENVIRONMENT', env('APP_ENV')),
    'release' => env('APM_RELEASE'),

    'tracking' => [
        'log_successful_requests' => true,
        'log_failed_requests' => true,
        'log_slow_requests' => true,
        'slow_threshold' => env('APM_SLOW_THRESHOLD', 1000), // in milliseconds
    ],

    // Sampling Configuration
    'sample_rate' => env('APM_SAMPLE_RATE', 1.0),
    'traces_sample_rate' => env('APM_TRACES_SAMPLE_RATE', 0.1),

    // Privacy & Security
    'send_default_pii' => env('APM_SEND_DEFAULT_PII', false),
    'sanitize' => [
        'keys' => ['password', 'token', 'secret', 'password_confirmation'],
        'headers' => ['authorization', 'cookie', 'x-xsrf-token'],
    ],

    // Ignored Routes/Paths
    'ignore_transactions' => [
        '/health',
        '/up',
        '_debugbar',
    ],

    // Breadcrumbs Configuration
    'breadcrumbs' => [
        'enabled' => env('APM_BREADCRUMBS_ENABLED', true),
        'logs' => env('APM_BREADCRUMBS_LOGS_ENABLED', true),
        'sql_queries' => env('APM_BREADCRUMBS_SQL_ENABLED', true),
        'sql_bindings' => env('APM_BREADCRUMBS_SQL_BINDINGS', false),
        'cache' => env('APM_BREADCRUMBS_CACHE_ENABLED', true),
        'queue_info' => env('APM_BREADCRUMBS_QUEUE_ENABLED', true),
        'http_client_requests' => env('APM_BREADCRUMBS_HTTP_CLIENT_ENABLED', true),
    ],

    // Performance Tracing
    'tracing' => [
        'enabled' => env('APM_TRACING_ENABLED', true),
        'sql_queries' => env('APM_TRACE_SQL_ENABLED', true),
        'sql_bindings' => env('APM_TRACE_SQL_BINDINGS', false),
        'views' => env('APM_TRACE_VIEWS_ENABLED', true),
        'cache' => env('APM_TRACE_CACHE_ENABLED', true),
        'queue_jobs' => env('APM_TRACE_QUEUE_ENABLED', true),
        'http_client_requests' => env('APM_TRACE_HTTP_CLIENT_ENABLED', true),
    ],

    // Storage Configuration
    'storage' => [
        'driver' => env('APM_STORAGE_DRIVER', 'redis'),
        'redis' => [
            'connection' => env('APM_REDIS_CONNECTION', 'default'),
            'retention_days' => env('APM_RETENTION_DAYS', 7),
        ],
    ],

    // Performance Thresholds (in milliseconds)
    'performance' => [
        'slow_request_threshold' => env('APM_SLOW_REQUEST_THRESHOLD', 1000),
        'slow_query_threshold' => env('APM_SLOW_QUERY_THRESHOLD', 100),
        'slow_job_threshold' => env('APM_SLOW_JOB_THRESHOLD', 10000),
    ],
];