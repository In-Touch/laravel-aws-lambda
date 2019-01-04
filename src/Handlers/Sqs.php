<?php

namespace Intouch\LaravelAwsLambda\Handlers;

use Illuminate\Foundation\Application;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Intouch\LaravelAwsLambda\Contracts\Handler;
use Intouch\LaravelAwsLambda\Queue\Jobs\LambdaSqsJob;
use Intouch\LaravelAwsLambda\Queue\Jobs\LambdaSqsJobFiveOne;

class Sqs extends Handler
{
    /**
     * @param Application $app
     * @param Worker $worker
     *
     * @return array|null
     * @throws \Throwable
     */
    public function handle(Application $app, Worker $worker)
    {
        if (version_compare($app->version(), '5.3.0', '>=')) {
            $job = new LambdaSqsJob($app, $this->payload);

            return $worker->process('lambda', $job, new WorkerOptions());
        } else {
            $job = new LambdaSqsJobFiveOne($app, $this->payload);

            return $worker->process('lambda', $job);
        }

    }

    public function canHandle()
    {
        return array_key_exists('eventSource', $this->payload) && $this->payload['eventSource'] == 'aws:sqs';
    }
}
