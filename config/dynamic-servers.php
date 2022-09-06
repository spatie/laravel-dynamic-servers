<?php

return [
    'providers' => [
        'your_provider' => [
            'class' => App\Support\YourProvider::class,
            'options' => [
                'username' => env('YOUR_PROVIDER_USER_NAME'),
                'password' => env('YOUR_PROVIDER_USER_NAME'),
            ],
        ]
    ],

    /*
     * Overriding these actions will give you fine-grained control over
     * how we handle your servers. In most cases, it's fine to use
     * the defaults.
     */
    'actions' => [
        'generate_server_name' => Spatie\DynamicServers\Actions\GenerateServerNameAction::class,
        'start_server' => Spatie\DynamicServers\Actions\StartServerAction::class,
        'stop_server' => Spatie\DynamicServers\Actions\StopServerAction::class,
        'find_servers_to_stop' => Spatie\DynamicServers\Actions\FindServersToStopAction::class,
        'reboot_server' => Spatie\DynamicServers\Actions\RebootServerAction::class,
    ],

    /*
     * Overriding these jobs will give you fine-grained control over
     * how we create, stop, delete and reboot your servers. In most cases,
     * it's fine to use the defaults.
     */
    'jobs' => [
        'create_server' => Spatie\DynamicServers\Jobs\CreateServerJob::class,
        'verify_server_started' => Spatie\DynamicServers\Jobs\VerifyServerStartedJob::class,
        'stop_server' => Spatie\DynamicServers\Jobs\StopServerJob::class,
        'verify_server_stopped' => Spatie\DynamicServers\Jobs\VerifyServerStoppedJob::class,
        'delete_server' => Spatie\DynamicServers\Jobs\DeleteServerJob::class,
        'verify_server_deleted' => Spatie\DynamicServers\Jobs\VerifyServerDeletedJob::class,
        'reboot_server' => Spatie\DynamicServers\Jobs\RebootServerJob::class,
        'verify_server_rebooted' => Spatie\DynamicServers\Jobs\VerifyServerRebootedJob::class,
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
     * Using Laravel's prune command all stopped servers will be deleted
     * after the given amount of days.
     */
    'prune_stopped_servers_from_local_db_after_days' => 7,

    'throw_exception_when_hitting_maximum_server_limit' => false,
];
