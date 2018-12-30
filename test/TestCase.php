<?php

namespace Intouch\LaravelAwsLambda\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }
}
