---
title: Manually increasing and decreasing servers
weight: 6
---

Instead of relying on the `ensure` method, you can manually increase and decrease servers.

```php
use Spatie\DynamicServers\Facades\DynamicServers;

DynamicServers::increase() // add one more default server
DynamicServers::increase(3) // add three more default servers
DynamicServers::increase(type: 'big') // add one more `big` server
DynamicServers::increase(5, type: 'big') // add five more `big` servers

DynamicServers::decrease() // destroy one default server
DynamicServers::decrease(2) // destroy two default servers
DynamicServers::decrease(type: 'big') // destroy one `big` server
DynamicServers::decrease(3, type: 'big') // destroy one `big` servers
```

Be aware that calling `ensure($numberOfServers)` will simply ensure that there are the number of servers giving available. It doesn't take into account any calls you made to `increase` or `decrease` previously. 

## Determine how many servers are provisioned

To know how many dynamic servers are provisioned (servers that are starting, running or rebooting) you can call `provisionedCount`:

```php
use Spatie\DynamicServers\Facades\DynamicServers;

DynamicServers::provisionedCount(); // returns an int
```
