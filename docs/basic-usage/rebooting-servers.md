---
title: Rebooting server
weight: 3
---

Should you for some reason have the reboot servers, you can do so by calling `reboot`.

```php
use \Spatie\DynamicServers\Facades\DynamicServers;

DynamicServers::reboot();
```

This will reboot any servers that are currently running. Server that are currently starting, will be rebooted as soon as their starting procedure is complete.
