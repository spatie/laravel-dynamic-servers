---
title: Using multiple server types
weight: 5
---

When configuring the package, you probably will have [configured a default server type](/docs/laravel-dynamic-servers/v1/basic-usage/configuring-your-first-server-type).

The package can handle multiple server types. Here's how you can configure another server type using the `new` method.

```php
$serverType = ServerType::new('big')
    
    /*
     *  The provider name given should match one of the providers in the
     * `providers` key of the `dynamic-servers` config file.
     */
    ->provider('up_cloud') 
    ->configuration(function (Server $server) {
        /*
         * These values can be used in the server provider
         */
        return [
                // whatever you like
            ],
        ];
    });
 
DynamicServers::registerServerType($serverType);
```

Most methods on `DynamicServers` accept a parameter to specify a server type. When you specify a server type to any of these methods, servers of another type will not be touched.

```php
use Spatie\DynamicServers\Facades\DynamicServers;

/*
 * This will ensure that 5 servers of the `default` server type will be available
 * Other server types will not be touched.
 */
DynamicServers::ensure(5) 

/*
 * This will ensure that 5 servers of the `big` server type will be available
 * Other server types will not be touched.
 */
DynamicServers::ensure(3, 'big') 

DynamicServers::reboot() // reboot all servers of type `default`
DynamicServers::reboot('big') // reboot all servers of type `big`

DynamicServers::increase() // add one more default server
DynamicServers::increase(type: 'big') // add one more `big` server

DynamicServers::decrease() // destroy one default server
DynamicServers::decrease(type: 'big') // destroy one `big` server
```


