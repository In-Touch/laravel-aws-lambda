<?php

namespace Intouch\LaravelAwsLambda\Contracts;

abstract class Handler
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
