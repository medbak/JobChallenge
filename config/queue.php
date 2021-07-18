<?php

return [

    'default' => "beanstalkd",

    'connections' => [
        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
        ],
    ],


];
