---
title: Configuring your first server type
weight: 2
---

Before starting to use the package, you should configure the `default` server type. This server type defines which hosting provider should be used, and the payload that should be sent to your hosting provider when creating a new service. 

Here's an example that sets up a default server type using the `up_cloud` server provider. Want to use another server provider? No problem, here's how you can [add support for your favourite provider](/docs/laravel-dynamic-servers/v1/advanced-usage/creating-your-own-server-provider).

The following configuration has a payload that will set up a new server by cloning a given image.

```php
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;
use \Spatie\DynamicServers\Support\DynamicServersManager;

// typically, in `app/Providers/DynamicServerProvider`

/*
 * Let's set up the default server type  
 */
$serverType = ServerType::default()
    
    /*
     *  The provider name given should match one of the providers in the
     * `providers` key of the `dynamic-servers` config file.
     */
     *  
    ->provider('up_cloud') 
    ->configuration(function (Server $server) {
        /*
         * These values will be given to the Upcloud API when creating a server
         */
        return [
            'server' => [
                'zone' => 'de-fra1',
                'title' => $server->name,
                'hostname' => Str::slug($server->name),
                'plan' => '2xCPU-4GB',
                'storage_devices' => [
                    'storage_device' => [
                        [
                            'action' => 'clone',
                            /*
                             * You can use $server->option() to grab on of the values
                             * that is configured for that provider in the
                             * `dynamic_servers` config key
                             */
                            'storage' => $server->option('disk_image'),
                            'title' => Str::slug($server->name) . '-disk',
                            'tier' => 'maxiops',
                        ],
                    ],
                ],
            ],
        ];
    });

/*
 * Register the defined server type, so we can use it throughout the package
 */    
DynamicServers::registerServerType($serverType);
```

You can test if everything is configured correctly by manually increase the number of dynamic servers. Run this piece of code somewhere (maybe in a Tinker session or something similar).

```php
Spatie\DynamicServers\Facades\DynamicServers::increase();
```

If everything is set up correctly, you should see a server spinning up at your hosting server. If nothing should happen, make sure your queues are running as starting/stopping servers uses the queue.

When the server has been started, you can use this code to destroy it. Rest assured that the package will only destroy servers that it has created itself. Any pre-existing servers in your hosting provider account will not be touched.

```php
Spatie\DynamicServers\Facades\DynamicServers::decrease();
```
