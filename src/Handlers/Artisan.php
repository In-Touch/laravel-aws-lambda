<?php

namespace Intouch\LaravelAwsLambda\Handlers;

use Illuminate\Contracts\Console\Kernel;
use Intouch\LaravelAwsLambda\Contracts\Handler;

class Artisan extends Handler
{
    public function canHandle($payload)
    {
        return array_key_exists('command', $payload);
    }

    public function handle(Kernel $kernel)
    {
        $result = $kernel->call($this->payload['command']);

        $kernel->terminate(null, null);

        return $result;
    }
}
