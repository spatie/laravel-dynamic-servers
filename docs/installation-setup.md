---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-dynamic-servers
```

## Migrating the database

This package will keep track of all dynamic servers in the `dynamic_servers` table. To create that table, run these
commands:

```bash
php artisan vendor:publish --tag="dynamic-servers-migrations"
php artisan migrate
```

## Publishing the config file

You can publish the `dynamic-servers` config file with this command.

```bash
php artisan vendor:publish --tag="dynamic-servers-config"
```

This is the content of the published config file:

```php
// coming soon
```

## Scheduling commands

The `MonitorDynamicServersCommand` command will take care of creating and destroying servers.

The `HandleHangingServersCommand` command will detect any servers that are starting and stopping, but never did start or stop completely.

You should add the commands to your schedule, and let them run every minute.

```php
// in app/Console/Kernel.php
use Spatie\DynamicServers\Commands\MonitorDynamicServersCommand;
use Spatie\DynamicServers\Commands\DetectHangingServersCommand;

protected function schedule(Schedule $schedule)
{
    $schedule->command(MonitorDynamicServersCommand::class)->everyMinute();
    $schedule->command(DetectHangingServersCommand::class)->everyMinute();
}
```
