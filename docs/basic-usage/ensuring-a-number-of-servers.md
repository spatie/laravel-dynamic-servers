---
title: Ensuring a number of servers
weight: 3
---

You can use the `ensure($numberOfServerNeeded)` method to make sure that the given number of servers are available.

```php
use Spatie\DynamicServers\Facades\DynamicServers;

/*
 * The package will make sure that 5 servers are available.
 * 
 * If there currently are less than 5, more servers will be started
 * If there currently are more than 5, some servers will be deleted
 */
DynamicServers::ensure(5);
```

There's also a  `determineServerCount` function that accepts a callable. That callable will be executed each minute by the `MonitorDynamicServersCommand` you scheduled when configuring the package.

Here's how you could use `ensure` with the callable passed to `determineServerCount`.

```php
// typically, in the `DynamicServersProvider` or a service provider of your own

use Laravel\Horizon\WaitTimeCalculator;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Support\DynamicServersManager;

/*
 * The package will call the closure passed to `determineServerCount` every minute
 */
DynamicServers::determineServerCount(function(DynamicServersManager $servers) {
   /*
    * First, we'll calculate the number of servers needed. 
    * 
    * In this example, we will take a look at Horizon's reported waiting time.
    * Of course, in your project you can calculate the number of servers needed however you want.    
    */
    $waitTimeInMinutes = app(WaitTimeCalculator::class)->calculate('default');
    
    $numberOfServersNeeded = round($waitTimeInMinutes / 10);

   /*
    * Next, we will pass the number of servers needed to the `ensure` method.
    */
    $servers->ensure($numberOfServersNeeded);
});
```

In addition to using `determineServerCount`, you could also listen for  Horizon's `LongWaitDetected` event. This way, servers will be started immediately when your queue grows long, and we don't have to wait until the schedule is called.

```php
use Illuminate\Support\Facades\Event;
use Laravel\Horizon\Events\LongWaitDetected;
use Spatie\DynamicServers\Facades\DynamicServers;

Event::listen(function (LongWaitDetected $event) {
    $waitTimeInMinutes = app(WaitTimeCalculator::class)->calculate('default');
    
    $numberOfServersNeeded = round($waitTimeInMinutes / 10);
    
    DynamicServers::ensure($numberOfServersNeeded);
});
```
