<?php

namespace Spatie\DynamicServers\Exceptions;

use Exception;

class JobDoesNotExist extends Exception
{
    public static function make(string $jobName)
    {
        return new self("There is no job named `{$jobName}`");
    }
}
