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
                /*
                 * Path to local certificate file on filesystem. It must be a PEM encoded file which
                 * contains your certificate and private key. It can optionally contain the
                 * certificate chain of issuers. The private key also may be contained
                 * in a separate file specified by local_pk.
                 */
                'local_cert' => env('SWARM_SERVER_TLS_CERT', null),

                /*
                 * Path to local private key file on filesystem in case of separate files for
                 * certificate (local_cert) and private key.
                 */
                'local_pk' => env('SWARM_SERVER_LOCAL_PK', null),

                /*
                 * Passphrase for your local_cert file.
                 */
                'passphrase' => env('SWARM_SERVER_PASSPHRASE', null),

                // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_SERVER
            ]),
        ],
    ],


    /*
     |--------------------------------------------------------------------------
     | Allowed Origins
     |--------------------------------------------------------------------------
     |
     | This array contains the hosts of which you want to allow incoming requests.
     | Leave this empty if you want to accept requests from all hosts.
     */
    'allowed_origins' => [
        //
    ],

    /*
     |--------------------------------------------------------------------------
     | Request Size
     |--------------------------------------------------------------------------
     |
     | The maximum request size in kilobytes that is allowed for an incoming WebSocket request.
     */
    'max_request_size_in_kb' => 250,
];
