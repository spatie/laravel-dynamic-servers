---
title: Overriding actions and jobs
weight: 5
---

You can have fine-grained control over how this package works under the hood. Most of its behaviour is implemented in actions and jobs that are registered in the `actions` and `jobs` config keys of the `dynamic-servers` config file.

You can override the behaviour by creating a class of your own that extends one of the defaults, and use your own class name in the config file.

Let's for example customize the `GenerateServerNameAction` class.

First, create a class that extends the default one and add your own implementation

```php
namespace App\Support;

use Spatie\DynamicServers\Actions\GenerateServerNameAction as BaseAction;

use Spatie\DynamicServers\Actions\GenerateServerNameAction;

class MyCustomGenerateServerNameAction extends BaseAction
{
        public function execute(Server $server): string
    {
        return "jolly-good-server-{$server->type}-{$server->id}";
    }
}
```

Next, specify you class in the config file.

```php
// in config/dynamic-servers.php

return [
// ... other options

    'actions' => [
        'generate_server_name' => App\Support\MyCustomGenerateServerNameAction::class,
        
        // ... other actions
];
```
