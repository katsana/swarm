<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Server Configuration
     |--------------------------------------------------------------------------
     |
     | Define the server configuration including port number, SSL support etc.
     |
     */
    'server' => [
        'host' => env('SWARM_SERVER_HOST', '127.0.0.1'),
        'port' => env('SWARM_SERVER_PORT', 8085),
        'secure' => env('SWARM_SERVER_SECURE', false),
        'options' => [
            'tls' => array_filter([
                'local_cert' => env('SWARM_SERVER_TLS_CERT', null),
                // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_SERVER
            ]),
        ],
    ],

    /*
     * This array contains the hosts of which you want to allow incoming requests.
     * Leave this empty if you want to accept requests from all hosts.
     */
    'allowed_origins' => [
        //
    ],

    /*
     * The maximum request size in kilobytes that is allowed for an incoming WebSocket request.
     */
    'max_request_size_in_kb' => 250,
];
