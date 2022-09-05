<?php

namespace Spatie\DynamicServers\Enums;

enum ServerStatus: string
{
    case New = 'new';
    case Starting = 'starting';
    case Running = 'running';
    case Paused = 'paused';
    case Stopping = 'stopping';
    case Stopped = 'stopped';
    case Deleting = 'deleting';
    case Deleted = 'deleted';
    case Errored = 'errored';
    case Rebooting = 'rebooting';
    case Hanging = 'hanging';

    public static function provisionedStates(): array
    {
        return [
            self::Starting,
            self::Running,
            self::Rebooting,
        ];
    }
}
