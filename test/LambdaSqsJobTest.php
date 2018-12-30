<?php

namespace Intouch\LaravelAwsLambda\Test\Handlers;

use Intouch\LaravelAwsLambda\Test\TestCase;
use Intouch\LaravelAwsLambda\Queue\Jobs\LambdaSqsJob;

class LambdaSqsJobTest extends TestCase
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
        $job = \Mockery::mock('Intouch\LaravelAwsLambda\Queue\Jobs\LambdaSqsJob',
            [
                $container,
                json_decode($this->validJson, true),
            ])->makePartial()->shouldAllowMockingProtectedMethods();

        $job->shouldReceive('resolveAndFire')->once()->with([
            'job' => 'CallQueuedHandler@call',
            'data' => ['command' => 'A serialized payload'],
        ]);

        $this->assertNull($job->fire());
    }

    /** @test */
    public function get_raw_body_call_should_properly_pass_lowercase_body_param()
    {
        $payload = json_decode($this->validJson, true);
        $job = new LambdaSqsJob(\Mockery::mock('Illuminate\Container\Container'), $payload);

        $this->assertEquals(
            '{"job": "CallQueuedHandler@call", "data": { "command": "A serialized payload" } }',
            $job->getRawBody());
    }

    /** @test */
    public function get_raw_body_call_should_properly_pass_uppercase_body_param()
    {
        $payload = json_decode($this->validJson, true);
        $payload['Body'] = $payload['body'];
        unset($payload['body']);

        $job = new LambdaSqsJob(\Mockery::mock('Illuminate\Container\Container'), $payload);

        $this->assertArrayNotHasKey('body', $payload);
        $this->assertEquals(
            '{"job": "CallQueuedHandler@call", "data": { "command": "A serialized payload" } }',
            $job->getRawBody()
        );
    }

    /** @test */
    public function it_should_return_attempts_as_integer()
    {
        $payload = json_decode($this->validJson, true);
        $job = new LambdaSqsJob(\Mockery::mock('Illuminate\Container\Container'), $payload);

        $this->assertEquals(1, $job->attempts());
    }

    /** @test */
    public function it_should_return_attempts_as_integer_with_uppercase_attributes()
    {
        $payload = json_decode($this->validJson, true);
        $payload['Attributes'] = $payload['attributes'];
        unset($payload['attributes']);

        $job = new LambdaSqsJob(\Mockery::mock('Illuminate\Container\Container'), $payload);

        $this->assertArrayNotHasKey('attributes', $payload);
        $this->assertEquals(1, $job->attempts());
    }

    /** @test */
    public function it_should_return_job_id()
    {
        $payload = json_decode($this->validJson, true);
        $job = new LambdaSqsJob(\Mockery::mock('Illuminate\Container\Container'), $payload);

        $this->assertEquals('19dd0b57-b21e-4ac1-bd88-01bbb068cb78', $job->getJobId());
    }

    /** @test */
    public function it_should_return_job_id_from_uppercase_message_id()
    {
        $payload = json_decode($this->validJson, true);
        $payload['MessageId'] = $payload['messageId'];
        unset($payload['messageId']);

        $job = new LambdaSqsJob(\Mockery::mock('Illuminate\Container\Container'), $payload);

        $this->assertArrayNotHasKey('messageId', $payload);
        $this->assertEquals('19dd0b57-b21e-4ac1-bd88-01bbb068cb78', $job->getJobId());
    }

    /** @test */
    public function it_should_return_constructor_container()
    {
        $payload = json_decode($this->validJson, true);
        $container = \Mockery::mock('Illuminate\Container\Container');
        $job = new LambdaSqsJob($container, $payload);

        $this->assertEquals($container, $job->getContainer());
    }

    /** @test */
    public function it_should_return_raw_sqs_job()
    {
        $payload = json_decode($this->validJson, true);
        $container = \Mockery::mock('Illuminate\Container\Container');
        $job = new LambdaSqsJob($container, $payload);

        $this->assertEquals($payload, $job->getSqsJob());
    }
}
