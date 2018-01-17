<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:default' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 60,
        'failed' => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    'environments' => [
        'production' => [
            'update-web-product-and-default' => [
                'connection' => 'redis',
                'queue' => ['default', 'update-web-product'],
                'balance' => 'simple',
                'processes' => 10,
                'tries' => 3,
            ],
            'update-web-category' => [
                'connection' => 'redis',
                'queue' => ['update-web-category'],
                'balance' => 'simple',
                'processes' => 1,
                'tries' => 3,
            ],
            'scrape-web-product' => [
                'connection' => 'redis',
                'queue' => ['scrape-web-product'],
                'balance' => 'simple',
                'processes' => 5,
                'tries' => 3,
            ],
            'scrape-web-category' => [
                'connection' => 'redis',
                'queue' => ['scrape-web-category'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
            'crawl-proxy' => [
                'connection' => 'redis',
                'queue' => ['crawl-proxy'],
                'balance' => 'simple',
                'processes' => 5,
                'tries' => 5,
            ],
            'clean-proxy' => [
                'connection' => 'redis',
                'queue' => ['clean-proxy'],
                'balance' => 'simple',
                'processes' => 1,
                'tries' => 2,
            ],
        ],

        'local' => [
            'update-web-product-and-default' => [
                'connection' => 'redis',
                'queue' => ['default', 'update-web-product'],
                'balance' => false,
                'processes' => 10,
                'tries' => 3,
                'timeout' => 1800,
            ],
            'update-web-category' => [
                'connection' => 'redis',
                'queue' => ['update-web-category'],
                'balance' => 'simple',
                'processes' => 1,
                'tries' => 3,
                'timeout' => 1800,
            ],
            'scrape-web-product' => [
                'connection' => 'redis',
                'queue' => ['scrape-web-product'],
                'balance' => 'simple',
                'processes' => 8,
                'tries' => 3,
                'timeout' => 1800,
            ],
            'scrape-web-category' => [
                'connection' => 'redis',
                'queue' => ['scrape-web-category'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
                'timeout' => 1800,
            ],
            'crawl-proxy' => [
                'connection' => 'redis',
                'queue' => ['crawl-proxy'],
                'balance' => 'simple',
                'processes' => 5,
                'tries' => 5,
            ],
            'clean-proxy' => [
                'connection' => 'redis',
                'queue' => ['clean-proxy'],
                'balance' => 'simple',
                'processes' => 1,
                'tries' => 2,
            ],
        ],
    ],
];
