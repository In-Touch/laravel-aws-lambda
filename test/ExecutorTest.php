<?php

namespace Intouch\LaravelAwsLambda\Test;

use Intouch\LaravelAwsLambda\Contracts\Handler;
use Intouch\LaravelAwsLambda\Executor;

class ExecutorTest extends TestCase
{
    /**
     * @param array $handlers
     * @return \Mockery\Mock
     */
    protected function setupMocks(array $handlers)
    {
        $container = \Mockery::mock('Illuminate\Container\Container')->makePartial();
        $container->shouldReceive('make')->with('config')
            ->andReturn($config = \Mockery::mock('Illuminate\Config\Repository'));
        $config->shouldReceive('get')->once()->with('aws-lambda.handlers')->andReturn($handlers);

        return $container;
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage Error running handler!
     */
    public function it_should_never_catch_exceptions()
    {
        $handlers = [
            FailingHandler::class,
        ];

        $container = $this->setupMocks($handlers);

        $executor = new Executor($container);
        $executor->handle([]);
    }

    /** @test */
    public function it_should_find_correct_handler_skipping_one_which_cant_handle()
    {
        $handlers = [
            CanNotHandleHandler::class,
            CanHandleHandler::class,
        ];

        $container = $this->setupMocks($handlers);

        $executor = new Executor($container);
        $return = $executor->handle([]);

        $this->assertEquals('Handled!', $return);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage No valid handler found for message
     */
    public function it_should_raise_an_exception_if_no_valid_handlers_found()
    {
        $handlers = [
            CanNotHandleHandler::class,
        ];

        $container = $this->setupMocks($handlers);

        $executor = new Executor($container);
        $executor->handle([]);
    }

    /** @test */
    public function it_should_iterate_on_multiple_records()
    {
        $handlers = [
            CountHandler::class,
        ];

        $container = $this->setupMocks($handlers);

        $executor = new Executor($container);
        $executor->handle(['Records' => [['record1'], ['record2']]]);

        $this->assertEquals(2, CountHandler::$runCount);
    }
}

class FailingHandler extends Handler
{
    public function canHandle()
    {
        return true;
    }

    public function handle()
    {
        throw new \Exception('Error running handler!');
    }
}

class CanNotHandleHandler extends Handler
{
    public function canHandle()
    {
        return false;
    }

    public function handle()
    {
        throw new \Exception('This exception shouldn\'t be invoked, because the canHandle is false!');
    }
}

class CanHandleHandler extends Handler
{
    public function canHandle()
    {
        return true;
    }

    public function handle()
    {
        return 'Handled!';
    }
}

class CountHandler extends Handler
{
    public static $runCount = 0;

    public function canHandle()
    {
        return true;
    }

    public function handle()
    {
        return ++self::$runCount;
    }
}
