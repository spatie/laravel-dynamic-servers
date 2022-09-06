# Dynamically create and destroy servers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-dynamic-servers.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-dynamic-servers)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-dynamic-servers/run-tests?label=tests)](https://github.com/spatie/laravel-dynamic-servers/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-dynamic-servers/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/spatie/laravel-dynamic-servers/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-dynamic-servers.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-dynamic-servers)

This package can help you start and stop servers when you need them. The prime use case is to spin up extra working
servers that can help you process the workload on queues.

Typically, on your hosting provider, you would prepare a server snapshot, that will be used as a template when starting
new servers.

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

Out of the box, the package supports [UpCloud](https://upcloud.com). You can
create [your own server provider](https://spatie.be/docs/laravel-dynamic-servers/v1/advanced-usage/creating-your-own-server-provider)
to add support for your favourite hosting service.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-dynamic-servers.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-dynamic-servers)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can
support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.
You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards
on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/laravel-dynamic-servers/).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Rias Van der Veken](https://twitter.com/riasvdv)
- [Freek Van der herten](https://twitter.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
