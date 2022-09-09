---
title: Fired Events
weight: 5
---

We will send out these events. They all have a `$server` property that has all info on the relevant server the event is fired for. They all live in the `Spatie\DynamicServers\Events` namespace

- `CreatingServerEvent`: a new server is being created at the hosting provider
- `ServerRunningEvent`: a server has been fully started
- `StoppingServerEvent`: a server is stopping at the hosting provider
- `ServerStoppedEvent`: a server has been stopped
- `DeletingServerEvent`: a server is being deleted at the hosting provider
- `ServerDeletedEvent`: a server has been deleted at the hosting provider
- `RebootingServerEvent`: a server is being rebooted at the hosting provider
- `ServerErroredEvent`: while calling the server provider API, we encountered an error. The error is saved in the exception attributes of a `$server`
- `ServerHangingEvent`: a server has been [marked as hanging](/docs/laravel-dynamic-servers/v1/advanced-usage/dealing-with-hanging-servers)
- `ServerLimitHit`: we could not create an extra server because of [the limit set](/docs/laravel-dynamic-servers/v1/advanced-usage/setting-a-server-limit).

