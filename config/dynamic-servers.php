<?php

return [
    'providers' => [
        'up_cloud' => [
            'class' => Spatie\DynamicServers\ServerProviders\UpCloud\UpCloud::class,
            'options' => [
                'username' => env('UP_CLOUD_USER_NAME'),
                'password' => env('UP_CLOUD_PASSWORD'),
                'disk_image' => env('UP_CLOUD_DISK_IMAGE_UUID'),
            ],
        ],
        'digital_ocean' => [
            'class' => Spatie\DynamicServers\ServerProviders\DigitalOcean\DigitalOcean::class,
            'options' => [
                'token' => env('DIGITAL_OCEAN_TOKEN'),
                'vpc_uuid' => env('DIGITAL_VPC_UUID'),
                'region' => env('DIGITAL_OCEAN_REGION', 'nyc3'),
                'size' => env('DIGITAL_OCEAN_SIZE', 's-1vcpu-1gb'),
                'image' => env('DIGITAL_OCEAN_IMAGE', 'ubuntu-20-04-x64'),
            ],
        ],
    ],

    'jobs' => [
        'create_server' => Spatie\DynamicServers\Jobs\CreateServerJob::class,
        'verify_server_started' => Spatie\DynamicServers\Jobs\VerifyServerStartedJob::class,
        'stop_server' => Spatie\DynamicServers\Jobs\StopServerJob::class,
        'verify_server_stopped' => Spatie\DynamicServers\Jobs\VerifyServerStoppedJob::class,
        'delete_server' => Spatie\DynamicServers\Jobs\DeleteServerJob::class,
        'verify_server_deleted' => Spatie\DynamicServers\Jobs\VerifyServerDeletedJob::class,
    ],
];
