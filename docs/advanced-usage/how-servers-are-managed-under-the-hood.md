---
title: How servers are managed under the hood
weight: 2
---

To use the package, you don't need to know how things are working on the hood. However, to troubleshoot things, or when you create your own server provider, it might be handy to know what is going on. On this page, we'll describe how the package works internally.

An important thing to know is that the package tracks state of all servers it has created in the `dynamic_servers` table.

## Creating and starting servers

Whenever you call `DynamicServers::increase()` (or `::ensure()` and it creates servers for you), we will create a new `Spatie\DynamicServers\Models\Server` model. When it is created it will have the status `new`.

Right after such a model is created, we will call `start()` on it.

```php
// happens internally

$server->start():
```

This will start a job, `Spatie\DynamicServers\Jobs\StopServer` that will call the `startServer` method on the `ServerProvider` class of the server. The `Server` model will now have status `starting`. 

Most hosting servers cannot start servers instantly. It takes a while to provision them. That's why the `CreateServerJob` will dispatch another job, `VerifyServerStartedJob` to verify that a server has been started. The  `VerifyServerStartedJob` will use the `hasStarted` method on the `ServerProvider` class to determine if the server has been fully started at the hosting provider. If the server has not started, the `VerifyServerStartedJob` will release it self, and another attempt will be made 20 seconds later.

If `hasStarted` returns `true`, then `VerifyServerStartedJob` will finish properly and the `Server` model will be marked as `running`.

## Stopping and deleting servers

Whenever you call `DynamicServers::decrease()` (or `::ensure()` and it removes servers for you), we will call `stop()` on those servers.

```php
// happens internally

$server->stop():
```

This will start a job, `Spatie\DynamicServers\Jobs\StopServerJob` that will call the `stopServer` method on the `ServerProvider` class of the server. The `Server` model will now have status `stopping`.

Most hosting servers cannot stop servers instantly. It takes a while to shut them down them. That's why the `StopServerJob` will dispatch another job, `VerifyServerStoppedJob` to verify that a server has been stopped. The  `VerifyServerStoppedJob` will use the `hasStopped` method on the `ServerProvider` class to determine if the server has been fully stopped by the hosting provider. If the server has not stopped, the `VerifyServerStoppedJob` will release itself, and another attempt will be made 20 seconds later.

If `hasStopped` returns `true`, then `VerifyServerStoppedJob` will mark `Server` model `stopped`. Next, the `DeleteServerJob` will be called. This job will call `deleteServer`  on the `ServerProvider` to delete the server.  The `Server` model will have status `deleting`

The `VerifyServerDeletedJob` will be dispatched to verify that a server has been deleting. The  `VerifyServerDeletedJob` will use the `hasBeenDeleted` method on the `ServerProvider` class to determine if the server has been fully stopped by the hosting provider. If the server has not stopped, the `VerifyServerDeletedJob` will release itself, and another attempt will be made 20 seconds later.

If `hasBeenDeleted` returns `true`, then the `Server` will have status `deleted`.
