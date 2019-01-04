<?php

namespace Intouch\LaravelAwsLambda\Test\Handlers;

use Intouch\LaravelAwsLambda\Handlers\Sqs;
use Intouch\LaravelAwsLambda\Test\TestCase;

class SqsHandlerTest extends TestCase
{
    // For testing we strip the Records[] off the record, and inject only the record
    // because of how the Executor strips that out and passes through individual
    // records.
    public $validJson = '
    {
      "messageId": "19dd0b57-b21e-4ac1-bd88-01bbb068cb78",
      "receiptHandle": "MessageReceiptHandle",
      "body": "{\"job\":\"Illuminate\\\\\\\\Queue\\\\\\\\CallQueuedHandler@call\",\"data\": { \"command\": \"O:16:\\\\\"App\\\\\\\\Jobs\\\\\\\\TestJob\\\\\":2:{s:5:\\\\\"queue\\\\\";N;s:5:\\\\\"delay\\\\\";N;}\"} }",
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
    public function can_handle_a_valid_sqs_message()
    {
        $payload = json_decode($this->validJson, true);

        $handler = new Sqs();
        $handler->setPayload($payload);

        $this->assertTrue($handler->canHandle());
    }

    /** @test */
    public function invokes_the_worker_process_on_a_sqs_job()
    {
        $worker = \Mockery::mock('Illuminate\Queue\Worker');
        $worker->shouldReceive('process')->once()->andReturn(['job' => 'jobObjectHere', 'failed' => false]);

        $payload = json_decode($this->validJson, true);

        $handler = new Sqs();
        $handler->setPayload($payload);
        $return = $handler->handle(\Mockery::mock('Illuminate\Container\Container'), $worker);

        $this->assertEquals(['job' => 'jobObjectHere', 'failed' => false], $return);
    }
}
