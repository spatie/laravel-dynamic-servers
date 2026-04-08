<?php

use Spatie\DynamicServers\Actions\FindServersToStopAction;
use Spatie\DynamicServers\Actions\GenerateServerNameAction;
use Spatie\DynamicServers\Actions\RebootServerAction;
use Spatie\DynamicServers\Actions\StartServerAction;
use Spatie\DynamicServers\Actions\StopServerAction;
use Spatie\DynamicServers\Jobs\CreateServerJob;
use Spatie\DynamicServers\Jobs\DeleteServerJob;
use Spatie\DynamicServers\Jobs\RebootServerJob;
use Spatie\DynamicServers\Jobs\StopServerJob;
use Spatie\DynamicServers\Jobs\VerifyServerDeletedJob;
use Spatie\DynamicServers\Jobs\VerifyServerRebootedJob;
use Spatie\DynamicServers\Jobs\VerifyServerStartedJob;
use Spatie\DynamicServers\Jobs\VerifyServerStoppedJob;
use Spatie\DynamicServers\ServerProviders\UpCloud\UpCloudServerProvider;

return [
    'providers' => [
        'up_cloud' => [
            'class' => UpCloudServerProvider::class,
            'maximum_servers_in_account' => 20,
            'options' => [
                'username' => env('UP_CLOUD_USER_NAME'),
                'password' => env('UP_CLOUD_PASSWORD'),
                'disk_image' => env('UP_CLOUD_DISK_IMAGE_UUID'),
            ],
        ],
    ],

    /*
     * Overriding these actions will give you fine-grained control over
     * how we handle your servers. In most cases, it's fine to use
     * the defaults.
     */
    'actions' => [
        'generate_server_name' => GenerateServerNameAction::class,
        'start_server' => StartServerAction::class,
        'stop_server' => StopServerAction::class,
        'find_servers_to_stop' => FindServersToStopAction::class,
        'reboot_server' => RebootServerAction::class,
    ],

    /*
     * Overriding these jobs will give you fine-grained control over
     * how we create, stop, delete and reboot your servers. In most cases,
     * it's fine to use the defaults.
     */
    'jobs' => [
        'create_server' => CreateServerJob::class,
        'verify_server_started' => VerifyServerStartedJob::class,
        'stop_server' => StopServerJob::class,
        'verify_server_stopped' => VerifyServerStoppedJob::class,
        'delete_server' => DeleteServerJob::class,
        'verify_server_deleted' => VerifyServerDeletedJob::class,
        'reboot_server' => RebootServerJob::class,
        'verify_server_rebooted' => VerifyServerRebootedJob::class,
    ],

    /**
     * Which queue the server jobs should be processed on
     * by default this will use your default queue.
     */
    'queue' => null,

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
