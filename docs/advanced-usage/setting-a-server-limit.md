---
title: Setting a server limit
weight: 6
---

You can set a hard limit on the amount of servers the package will create for a given server provider.

In the `dynamic-servers` config file you can set a `maximum_servers_in_account` value for each provider. By default, it is set to 20.

When we detect more that the given number of servers in your hosting server account we won't create any new servers, but we will fire the `Spatie\DynamicServers\Models\Server\ServerLimitHitEvent`.

If the `throw_exception_when_hitting_maximum_server_limit` is set to `true` in the config file, we'll also throw an exception.
