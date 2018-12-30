<?php

namespace Intouch\LaravelAwsLambda\Handlers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Queue\Worker;
use Intouch\LaravelAwsLambda\Contracts\Handler;
use Intouch\LaravelAwsLambda\Queue\Jobs\LambdaSqsJob;

class Sqs extends Handler
{
    /**
     * @param Container $container
     * @param Worker $worker
     *
     * @return array|null
     * @throws \Throwable
     */
    public function handle(Container $container, Worker $worker)
    {
        $job = new LambdaSqsJob($container, $this->payload);

        return $worker->process('lambda', $job);
    }

    public function canHandle()
    {
        return array_key_exists('eventSource', $this->payload) && $this->payload['eventSource'] == 'aws:sqs';
    }
}
