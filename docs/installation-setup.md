---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-dynamic-servers
```

## Run the installer

Next, you can run the installer with:

```bash
php artisan dynamic-servers:install
```

This command will:

- publish the migration that will create a `dynamic_servers` table in your database. This table is used to keep track of all servers created by the package.
- create the `dynamic-servers.php` config file in the `/config` directory of your

This is how the config file looks like (there are more install instructions after the contents of the config file).

```php
<?php

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
```

## Migrating the database

This package will keep track of all dynamic servers in the `dynamic_servers` table. To create that table, run these
commands:

```bash
php artisan migrate
```

## Configuring queues

This package uses queued jobs to start and stop servers. Make sure, you have configured [one of the available queuing mechanisms](https://laravel.com/docs/master/queues) in your Laravel app.

## Scheduling commands

You should register a couple of commands in your kernel schedule.

The `MonitorDynamicServersCommand` command will take care of creating and destroying servers.

The `HandleHangingServersCommand` command will detect any servers that are starting and stopping, but never did start or stop completely.

To clean up records of stopped servers in the `dynamic_servers` table, you should add Laravel's `model:prune` command. If you already have this command in your schedule, add the `\Spatie\DynamicServers\Models\Server::class` model to its options.

You should add the commands to your schedule, and let them run every minute.

```php
// in app/Console/Kernel.php
use Spatie\DynamicServers\Commands\MonitorDynamicServersCommand;
use Spatie\DynamicServers\Commands\DetectHangingServersCommand;
use Spatie\DynamicServers\Models\Server;

protected function schedule(Schedule $schedule)
{
    $schedule->command(MonitorDynamicServersCommand::class)->everyMinute();
    $schedule->command(DetectHangingServersCommand::class)->everyMinute();
    $schedule->command('model:prune', [
        '--model' => [Server::class],
    ])->daily();
}
```
