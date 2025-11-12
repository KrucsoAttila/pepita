<?php
return [

    'driver' => env('SCOUT_DRIVER', 'elastic'),

    'prefix' => env('SCOUT_PREFIX', ''),

    'queue' => env('SCOUT_QUEUE', false),

    'after_commit' => false,

    // Elasticsearch hostok M:N, vesszővel elválasztva
    'elasticsearch' => [
        'hosts' => array_map('trim', explode(',', env('ELASTICSEARCH_HOSTS', 'http://es:9200'))),
    ],
];
