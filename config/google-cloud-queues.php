<?php

return [
    'project_id' => env('GOOGLE_CLOUD_PROJECT_ID', 'your-project-id'),
    'key_file_path' => env('GOOGLE_CLOUD_QUEUES_KEY_FILE', null), // optional: /path/to/service-account.json
    'location' => env('GOOGLE_CLOUD_QUEUES_LOCATION', 'europe-west6'),
    'queue_prefix' => env('GOOGLE_CLOUD_QUEUES_PREFIX', 'google-cloud-queue--'),

    'rate_limits' => [
        'max_burst_size' => 10,
        'max_concurrent_dispatches' => 10,
        'max_dispatches_per_second' => 10,
    ],
    'retry_config' => [
        'max_attempts' => 5,
        'max_retry_duration' => 0,
        'min_backoff' => 1,
        'max_backoff' => 900,
        'max_doublings' => 16,
    ],

    'queues' => [
        [
            'name' => 'queue1',
            // 'rate_limits' => [
            //     'max_burst_size' => 10,
            //     'max_concurrent_dispatches' => 75,
            //     'max_dispatches_per_second' => 20,
            // ],
            // 'retry_config' => [
            //     'max_attempts' => 5,
            //     'max_retry_duration' => 0,
            //     'min_backoff' => 1,
            //     'max_backoff' => 3700,
            //     'max_doublings' => 16,
            // ],
        ],
        [
            'name' => 'queue2',
        ],
    ],
];
