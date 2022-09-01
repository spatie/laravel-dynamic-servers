<?php

namespace Spatie\DynamicServers\ServerProviders\UpCloud;

class UpCloudServer
{
    public function __construct(
        public string $uuid,
        public string $title,
        public string $ip,
        public UpCloudServerStatus $status,
    ) {
    }

    public static function fromApiPayload(array $payload): self
    {
        $ip = collect($payload['ip_addresses']['ip_address'])
            ->where('access', 'public')
            ->where('family', 'IPv4')
            ->first()['address'] ?? '';

        return new self(
            $payload['uuid'],
            $payload['title'],
            $ip,
            UpCloudServerStatus::from($payload['state']),
        );
    }
}
