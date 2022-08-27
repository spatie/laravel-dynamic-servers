<?php

namespace Spatie\DynamicServers\ServerProviders\UpCloud;

enum UpCloudServerStatus: string
{
    case Started = 'started';
    case Stopped = 'stopped';
    case Maintenance = 'maintenance';
}
