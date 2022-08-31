<?php

namespace Spatie\DynamicServers\ServerProviders\DigitalOcean;

use Spatie\LaravelData\Data;

class DigitalOceanServer extends Data
{
    public function __construct(
        public string $id,
        public string $title,
        public string $ip,
        public DigitalOceanServerStatus $status,
    ) {
    }

    public static function fromApiPayload(array $payload): self
    {
        $ip = collect($payload['networks']['v4'])
            ->where('type', 'public')
            ->first()['ip_address'] ?? '';

        return new static(
            $payload['id'],
            $payload['name'],
            $ip,
            DigitalOceanServerStatus::from($payload['state']),
        );
    }
}
