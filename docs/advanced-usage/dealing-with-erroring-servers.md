---
title: Dealing with erroring servers
weight: 3
---

Whenever the makes internally gets an exception (most probably because of the server provider API returning an error) we update the `Server` model with a status `errored`. We'll consider that this server is not operational, and we'll not count it when determine how many servers we should start, stop or reboot when calling `DynamicServers::ensure($number)`.

We will also fire an event `ServerErroredEvent` so you can take an appropriate action (sending a notification, manually deleting the server, ...). This event has two properties:

- `$server`: the server that is hanging
- `$previousStatus`: this can be `starting`, `stopping` or `rebooting`.
