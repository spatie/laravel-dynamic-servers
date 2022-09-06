---
title: Dealing with hanging servers
weight: 4
---

Starting, stopping, and rebooting server is an asynchronous process. The package uses the self-releasing jobs `VerifyServerStarted`, `VerifyServerStopped`, `VerifyServerDeleted` and `VerifyServerReboot` to determine if such an asynchronous process completes correctly (see ["How servers are managed under the hood"](/docs/laravel-dynamic-servers/v1/advanced-usage/how-servers-are-managed-under-the-hood)).

In the config file you can use the `mark_server_as_hanging_after_minutes` key to specify how long we should wait for such processes to complete. By default, we wait for 10 minutes.

When it takes longer than 10 minutes, we'll consider the server as hanging. In the `dynamic_servers` table we'll update the status of the server to `hanging`. We'll consider that this server is not operational, and we'll not count it when determine how many servers we should start, stop or reboot when calling `DynamicServers::ensure($number)`.

We will also fire an event `ServerHangingEvent` so you can take an appropriate action (sending a notification, manually deleting the server, ...). This event has two properties:

- `$server`: the server that is hanging
- `$previousStatus`: this can be `starting`, `stopping` or `rebooting`.
