<?php

namespace Intouch\LaravelAwsLambda\Test\Handlers;

use Intouch\LaravelAwsLambda\Test\TestCase;

class LambdaSqsJobFiveOneTest extends TestCase
{
    public $job;

    // For testing we strip the Records[] off the record, and inject only the record
    // because of how the Executor strips that out and passes through individual
    // records.
    public $validJson = '
    {
      "messageId": "19dd0b57-b21e-4ac1-bd88-01bbb068cb78",
      "receiptHandle": "MessageReceiptHandle",
      "body": "{\"job\": \"CallQueuedHandler@call\", \"data\": { \"command\": \"A serialized payload\" } }",
      "attributes": {
        "ApproximateReceiveCount": "1",
        "SentTimestamp": "1523232000000",
        "SenderId": "123456789012",
        "ApproximateFirstReceiveTimestamp": "1523232000001"
      },
      "messageAttributes": {},
      "md5OfBody": "7b270e59b47ff90a553787216d55d91d",
      "eventSource": "aws:sqs",
      "eventSourceARN": "arn:aws:sqs:us-east-1:123456789012:MyQueue",
      "awsRegion": "us-east-1"
    }';

    /** @test */
    public function it_should_pass_fire_up_to_resolve_and_fire_parent_method_with_valid_payload()
    {
        $container = \Mockery::mock('Illuminate\Container\Container');
        $container->shouldReceive('make')->with('CallQueuedHandler')->andReturn($queueHandler = \Mockery::mock('CallQueuedHandler'));
        $queueHandler->shouldReceive('call');

        $job = \Mockery::mock('Intouch\LaravelAwsLambda\Queue\Jobs\LambdaSqsJobFiveOne',
            [
                $container,
                json_decode($this->validJson, true),
            ])->makePartial()->shouldAllowMockingProtectedMethods();

        $job->shouldReceive('resolveAndFire')->with([
            'job' => 'CallQueuedHandler@call',
            'data' => ['command' => 'A serialized payload'],
        ]);

        $this->assertNull($job->fire());
    }
}
