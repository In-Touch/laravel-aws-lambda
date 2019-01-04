<?php

namespace Intouch\LaravelAwsLambda\Test\Handlers;

use Intouch\LaravelAwsLambda\Handlers\Artisan;
use Intouch\LaravelAwsLambda\Test\TestCase;

class ArtisanHandlerTest extends TestCase
{
    public $validJson = '
    {
      "command": "inspire"
    }';

    /** @test */
    public function can_handle_a_valid_artisan_message()
    {
        $payload = json_decode($this->validJson, true);

        $handler = new Artisan();
        $handler->setPayload($payload);
        $this->assertTrue($handler->canHandle());
    }

    /** @test */
    public function it_handles_an_artisan_message_correctly()
    {
        $kernel = \Mockery::mock('Illuminate\Foundation\Console\Kernel');
        $kernel->shouldReceive('call')->with('inspire')->once()->andReturn(0);
        $kernel->shouldReceive('terminate')->with(null, 0)->once();

        $payload = json_decode($this->validJson, true);

        $handler = new Artisan();
        $handler->setPayload($payload);

        $retval = $handler->handle($kernel);

        // Artisan commands should return an exit code of 0
        $this->assertEquals(0, $retval);
    }
}
