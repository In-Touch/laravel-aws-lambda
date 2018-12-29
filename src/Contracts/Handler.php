<?php

namespace Intouch\LaravelAwsLambda\Contracts;

abstract class Handler implements IHandler
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
