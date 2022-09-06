---
title: Using multiple server types
weight: 4
---

When configuring the package, you probably will have [configured a default server type](TODO: add link).

The package can handle multiple servers types. Here's how you can configure another server type using the `new` method.

```php
$serverType = ServerType::new('big')
    
    /*
     *  The provider name given should match one of the providers in the
     * `providers` key of the `dynamic-servers` config file.
     */
    ->provider('up_cloud') 
    ->configuration(function (DynamicServersManager $server) {
        /*
         * This payload will be given to the server provider when creating a server
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
DynamicServers::increase('big') // add one more `big` server

DynamicServers::decrease() // destroy one more default server
DynamicServers::decrease('big') // destroy one more `big` server
```


