<?php

namespace Spatie\DynamicServers\ServerProviders\DigitalOcean;

enum DigitalOceanServerStatus: string
{
    case New = 'new';
    case Active = 'active';
    case Off = 'off';
    case Archive = 'archive';
}
