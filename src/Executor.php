<?php

namespace Intouch\LaravelAwsLambda;

use Illuminate\Contracts\Container\Container;
use Intouch\LaravelAwsLambda\Contracts\Handler;

class Executor
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $handlers;

    /**
     * @var Container
     */
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->handlers = $app->make('config')->get('aws-lambda.handlers');
    }

    public function handle($payload)
    {
        if (array_key_exists('Records', $payload)) {
            $records = collect($payload['Records']);
            foreach ($records as $record) {
                $this->runHandlers($record);
            }
        } else {
            return $this->runHandlers($payload);
        }
    }

    private function runHandlers($payload)
    {
        foreach ($this->handlers as $handler) {
            /** @var Handler $instance */
            $instance = $this->app->make($handler);
            $instance->setPayload($payload);

            if ($instance->canHandle()) {
                return $this->app->call([$instance, 'handle']);
            }
        }

        throw new \Exception('No valid handler found for message');
    }
}
