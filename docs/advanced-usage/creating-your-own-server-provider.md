---
title: Creating your own server provider
weight: 1
---


Out of the box, this package supports [UpCloud](https://upcloud.com), but it's easy to support your favourite hosting service as well.

## Community maintained drivers

- [Digital Ocean](https://github.com/sidis405/laravel-dynamic-servers-digital-ocean)
- [Vultr](https://github.com/AbdullahFaqeir/laravel-dynamic-servers-vultr)

## Do you want to create a driver?

If you have created a custom server provider, do not PR it to this package. We don't want to maintain it.

Instead, put it in a repository of your own, and [mail us](mailto:info@spatie.be) a link to it. We'll put the link here in our docs, so others can make use of it.

## Creating a server provider class

First, you need a server provider class, which is any class that extends the abstract `Spatie\DynamicServers\ServerProviders\ServerProvider` class. 

By extending that class, you'll need to implement a couple of methods,
such as `createServer`, `hasStarted`, ... In these methods you'll probably need to use the API of your hosting service to do the work that's necessary.

Here's an example:

```php
namespace App\Support;

use Spatie\DynamicServers\ServerProviders\ServerProvider;

class YourServerProvider extends ServerProvider
{
    public function createServer(): void
    {
        // make an API call to create a server 
    }

    public function hasStarted(): bool
    {
        // make an API to determine if the server is ready to be used
    }

    public function stopServer(): void
    {
        // make the API call to stop the server
    }

    public function hasStopped(): bool
    {
        // make the API call to determine if the server has stopped
    }

    public function deleteServer(): void
    {
        // make the API call to delete the server
    }

    public function hasBeenDeleted(): bool
    {
        // make the API to determine if the server has been deleted
    }

    public function rebootServer(): void
    {
        // make the API call to reboot the server
    }

    public function currentServerCount(): int
    {
        // return the number of servers that is currently in your account
    }
}
```

The package stores information locally in the `dynamic_servers` on all server that should be created and have been created.

Your server has access to a property, `$server`, that you can use in your API calls. The `configuration` property will return the array that was returned by the `configuration` function when you [configure a server type](/docs/laravel-dynamic-servers/v1/basic-usage/configuring-your-first-server-type).

Let's create a dummy implementation of `createServer` and `hasStarted`.

```php
use \Illuminate\Support\Facades\Http;

public function createServer(): void
{
    /*
     * An array with configuration data that can be used in the creation call
     */
    $serverConfiguration = $this->server->configuration;

    /*
     * This is a dummy call, you should to the specific things
     * needed for your hosting provider
     */
    $response = Http::::withBasicAuth(
            /*
             * With the `option` method, you can get options defined 
             * for this provider in the config file. These sensitive
             * values are not stored in the database.
             */
            $this->server->option('username'),
            $this->server->option('password')
        )->post('https://your-provider.com/api/create-server', $serverConfiguration);

    /*
     * Your hosting provider will probably respond with some
     * data that can be identified the server that was created.
     * 
     * This data should be saved as metadata on the server instance,
     * so it can be used in future API call. You can use what ever
     * key name you like.
     */
     $this->server->addMeta('your_provider_server_properties', $response->toArray());
}

public function hasStarted(): bool
{
    /*
     * Get the id that your hosting provider assigned to the remote server.
     * You can use dot-notation to go into the saved properties in meta.
     * 
     * We use `server_id` here, but you should use whatever field name
     * your hosting provider returned
     */
    $serverId = $this->server->meta('your_provider_server_properties.server_id');
    
    /*
     * Get the fresh server details from your hosting provider.
     * 
     * Replace this with the call needed for your particular hosting provider
     */
    $serverDetails = Http::(
            $this->server->option('username'),
            $this->server->option('password')
        )->post("https://your-provider.com/api/servers/{$serverId}")->json();
    
    /*
     * We are assuming that the status is the `status` field and the value is `running`,
     * but you should do what is appropriate for your hosting provider.
     */
    return $serverDetails['status'] === 'running';
}
```

All other methods should be implemented in a similar fashion.

## Register your server provider class

Next, you'll need to register your provider in the config file. Make sure to define any options that you are using in your server provider with `$server->config(...)`

```php
// in config/dynamic-servers.php

return [
    'providers' => [
        'your_provider' => [
            'class' => App\Support\YourServerProvider::class,
            'options' => [
                'username' => env('YOUR_PROVIDER_USER_NAME'),
                'password' => env('YOUR_PROVIDER_USER_NAME'),
            ],
        ],
    ],
```

## Registering a server type

With this out of the way you can [register a server type](/docs/laravel-dynamic-servers/v1/basic-usage/configuring-your-first-server-type) that makes use of your server provider.

```php
use Spatie\DynamicServers\Facades\DynamicServers;
use Spatie\DynamicServers\Models\Server;
use Spatie\DynamicServers\Support\ServerTypes\ServerType;
use \Spatie\DynamicServers\Support\DynamicServersManager;

// typically, in `app/Providers/DynamicServerProvider

$serverType = ServerType::default()
    ->provider('your_provider') // matches the name in the config file 
    ->configuration(function (Server $server) {
        /*
         * This values will be saved in the `configuration` property
         * of a `$server` and can be used in the server provider.
         */
        return [
            
        ];
    });

/*
 * Register the defined server type, so we can use it throughout the package
 */    
DynamicServers::registerServerType($serverType);
```
