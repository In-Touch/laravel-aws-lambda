<?php

namespace Intouch\LaravelAwsLambda\Contracts;

abstract class Handler
{
    protected $payload;

    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    abstract function canHandle();

//    abstract function  handle();
}
