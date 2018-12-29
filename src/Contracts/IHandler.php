<?php

namespace Intouch\LaravelAwsLambda\Contracts;

interface IHandler
{
    public function canHandle();
}
