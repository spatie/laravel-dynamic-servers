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
    case Hanging = 'hanging';
}
