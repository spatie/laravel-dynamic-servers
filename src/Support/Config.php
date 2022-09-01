<?php

namespace Spatie\DynamicServers\Support;

use Exception;
use Illuminate\Support\Arr;
use Spatie\DynamicServers\Exceptions\CannotDetermineDefaultProviderName;
use Spatie\DynamicServers\Exceptions\InvalidAction;
use Spatie\DynamicServers\Exceptions\JobDoesNotExist;

class Config
{
    public static function providerOption(string $providerName, string $key = null): mixed
    {
        $providerOptions = config("dynamic-servers.providers.{$providerName}.options");

        return is_null($key)
            ? $providerOptions
            : Arr::get($providerOptions, $key);
    }

    public static function dynamicServerJobClass(string $jobName): mixed
    {
        $jobClass = config("dynamic-servers.jobs.{$jobName}");

        if (empty($jobClass)) {
            throw JobDoesNotExist::make($jobName);
        }

        return $jobClass;
    }

    public static function action(string $actionName): object
    {
        $actionClass = config("dynamic-servers.actions.{$actionName}");

        try {
            $action = app($actionClass);
        } catch (Exception $exception) {
            throw InvalidAction::make($actionName, $actionClass, $exception);
        }

        return $action;
    }

    public static function defaultProviderName(): string
    {
        $providerName = array_key_first(config('dynamic-servers.providers'));

        if (empty($providerName)) {
            throw CannotDetermineDefaultProviderName::make();
        }

        return $providerName;
    }

    public static function providerNames(): array
    {
        return array_keys(config('dynamic-servers.providers') ?? []);
    }
}
