<?php

namespace Intouch\LaravelAwsLambda\Handlers;

use Illuminate\Contracts\Console\Kernel;
use Intouch\LaravelAwsLambda\Contracts\Handler;

class Artisan extends Handler
{
    public function canHandle()
    {
        return array_key_exists('command', $this->payload);
    }

    public function handle(Kernel $kernel)
    {
        $result = $kernel->call($this->payload['command']);

        $kernel->terminate(null, $result);

        return $result;
    }
}
