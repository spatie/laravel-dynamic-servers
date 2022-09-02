<?php

// TODO: install command, copy service provider
// TODO: reboot (+ flag on model to reboot)

return [
    'providers' => [
        'up_cloud' => [
            'class' => Spatie\DynamicServers\ServerProviders\UpCloud\UpCloudServerProvider::class,
            'maximum_servers_in_account' => 20,
            'options' => [
                'username' => env('UP_CLOUD_USER_NAME'),
                'password' => env('UP_CLOUD_PASSWORD'),
                'disk_image' => env('UP_CLOUD_DISK_IMAGE_UUID'),
            ],
        ],
    ],

    'actions' => [
        'generate_server_name' => Spatie\DynamicServers\Actions\GenerateServerNameAction::class,
        'start_server' => Spatie\DynamicServers\Actions\StartServerAction::class,
        'stop_server' => Spatie\DynamicServers\Actions\StopServerAction::class,
        'find_servers_to_stop' => Spatie\DynamicServers\Actions\FindServersToStopAction::class,
    ],

    'jobs' => [
        'create_server' => Spatie\DynamicServers\Jobs\CreateServerJob::class,
        'verify_server_started' => Spatie\DynamicServers\Jobs\VerifyServerStartedJob::class,
        'stop_server' => Spatie\DynamicServers\Jobs\StopServerJob::class,
        'verify_server_stopped' => Spatie\DynamicServers\Jobs\VerifyServerStoppedJob::class,
        'delete_server' => Spatie\DynamicServers\Jobs\DeleteServerJob::class,
        'verify_server_deleted' => Spatie\DynamicServers\Jobs\VerifyServerDeletedJob::class,
    ],

    /*
     * When we detect that a server is taking longer than this amount of minutes
     * to start or stop, we'll mark it has hanging, and will not try to use it anymore
     *
     * The `ServerHangingEvent` will be fired, that you can use to send yourself a notification,
     * or manually take the necessary actions to start/stop it.
     */
    'mark_server_as_hanging_after_minutes' => 10,

    /*
     * The dynamic_servers table holds records of all dynamic servers.
     *
     * Using Laravels prune command all stopped servers will be deleted
     * after the given amount of days.
     */
    'prune_stopped_servers_from_local_db_after_days' => 7,

    'throw_exception_when_hitting_maximum_server_limit' => false,
];
