<?php

namespace Spatie\DynamicServers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\DynamicServers\Enums\ServerStatus;
use Spatie\DynamicServers\Models\Server;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition()
    {
        return [
            'name' => 'server-name',
            'provider' => 'up_cloud',
        ];
    }

    public function running(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ServerStatus::Running->value,
            ];
        });
    }
}
