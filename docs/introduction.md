---
title: Introduction
weight: 1
---

This package can help you start and stop servers when you need them. The prime use case is to spin up extra working servers, based on a server image, that can help you process the workload on queues. 

You can think of this as a sort of PHP based version of Kubernetes that has 5% of its features, but covers that 80% use case. For most PHP and Laravel developers, this package will also be easier to learn and use.

Typically, on your hosting provider, you would prepare a server snapshot, that will be used as a template when starting new servers.

After the package is configured, spinning up an extra servers is as easy as:

```php
// typically, in a service provider

use Laravel\Horizon\WaitTimeCalculator;
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Support\DynamicServersManager;

/*
 * The package will call the closure passed 
 * to `determineServerCount` every minute
 */
DynamicServers::determineServerCount(function(DynamicServersManager $servers) {
   /*
    * First, we'll calculate the number of servers needed. 
    * 
    * In this example, we will take a look at Horizon's 
    * reported waiting time. Of course, in your project you can 
    * calculate the number of servers needed however you want.    
    */
    $waitTimeInMinutes = app(WaitTimeCalculator::class)->calculate('default');
    $numberOfServersNeeded = round($waitTimeInMinutes / 10);

   /*
    * Next, we will pass the number of servers needed to the `ensure` method.
    * 
    * If there currently are less that that number of servers available,
    * the package will start new ones.
    * 
    * If there are currently more than that number of servers running,
    *  the package will stop a few servers.
    */
    $servers->ensure($numberOfServersNeeded);
});
```

Out of the box, the package supports [UpCloud](https://upcloud.com).  You can create [your own server provider](/docs/laravel-dynamic-servers/v1/advanced-usage/creating-your-own-server-provider) to add support for your favourite hosting service.


