<?php

namespace Intouch\LaravelAwsLambda\Test\Handlers;

use Intouch\LaravelAwsLambda\Handlers\ApiGateway;
use Intouch\LaravelAwsLambda\Test\TestCase;

class ApiGatewayHandlerTest extends TestCase
{
    public $validJson = '
    {
      "body": "",
      "resource": "/{proxy+}",
      "path": "/",
      "httpMethod": "GET",
      "isBase64Encoded": true,
      "queryStringParameters": {
        "foo": "bar"
      },
      "pathParameters": {
        "proxy": "/"
      },
      "stageVariables": {
        "baz": "qux"
      },
      "headers": {
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
        "Accept-Encoding": "gzip, deflate, sdch",
        "Accept-Language": "en-US,en;q=0.8",
        "Cache-Control": "max-age=0",
        "CloudFront-Forwarded-Proto": "https",
        "CloudFront-Is-Desktop-Viewer": "true",
        "CloudFront-Is-Mobile-Viewer": "false",
        "CloudFront-Is-SmartTV-Viewer": "false",
        "CloudFront-Is-Tablet-Viewer": "false",
        "CloudFront-Viewer-Country": "US",
        "Host": "1234567890.execute-api.us-east-1.amazonaws.com",
        "Upgrade-Insecure-Requests": "1",
        "User-Agent": "Custom User Agent String",
        "Via": "1.1 08f323deadbeefa7af34d5feb414ce27.cloudfront.net (CloudFront)",
        "X-Amz-Cf-Id": "cDehVQoZnx43VYQb9j2-nvCh-9z396Uhbp027Y2JvkCPNLmGJHqlaA==",
        "X-Forwarded-For": "127.0.0.1, 127.0.0.2",
        "X-Forwarded-Port": "443",
        "X-Forwarded-Proto": "https"
      },
      "requestContext": {
        "accountId": "123456789012",
        "resourceId": "123456",
        "stage": "prod",
        "requestId": "c6af9ac6-7b61-11e6-9a41-93e8deadbeef",
        "requestTime": "09/Apr/2015:12:34:56 +0000",
        "requestTimeEpoch": 1428582896000,
        "identity": {
          "cognitoIdentityPoolId": null,
          "accountId": null,
          "cognitoIdentityId": null,
          "caller": null,
          "accessKey": null,
          "sourceIp": "127.0.0.1",
          "cognitoAuthenticationType": null,
          "cognitoAuthenticationProvider": null,
          "userArn": null,
          "userAgent": "Custom User Agent String",
          "user": null
        },
        "path": "/",
        "resourcePath": "/{proxy+}",
        "httpMethod": "GET",
        "apiId": "1234567890",
        "protocol": "HTTP/1.1"
      }
    }';

    /** @test */
    public function can_handle_a_valid_api_gateway_message()
    {
        $payload = json_decode($this->validJson, true);

        $handler = new ApiGateway($payload);
        $this->assertTrue($handler->canHandle());
    }

    /** @test */
    public function it_ignores_a_payload_from_an_alb()
    {
        $payload = '
    {
      "body": "",
      "path": "/",
      "headers": {
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
        "Accept-Encoding": "gzip, deflate, sdch",
        "Accept-Language": "en-US,en;q=0.8",
        "Cache-Control": "max-age=0",
        "CloudFront-Forwarded-Proto": "https",
        "CloudFront-Is-Desktop-Viewer": "true",
        "CloudFront-Is-Mobile-Viewer": "false",
        "CloudFront-Is-SmartTV-Viewer": "false",
        "CloudFront-Is-Tablet-Viewer": "false",
        "CloudFront-Viewer-Country": "US",
        "Host": "1234567890.execute-api.us-east-1.amazonaws.com",
        "Upgrade-Insecure-Requests": "1",
        "User-Agent": "Custom User Agent String",
        "Via": "1.1 08f323deadbeefa7af34d5feb414ce27.cloudfront.net (CloudFront)",
        "X-Amz-Cf-Id": "cDehVQoZnx43VYQb9j2-nvCh-9z396Uhbp027Y2JvkCPNLmGJHqlaA==",
        "X-Forwarded-For": "127.0.0.1, 127.0.0.2",
        "X-Forwarded-Port": "443",
        "X-Forwarded-Proto": "https"
      },
      "requestContext": {
        "accountId": "123456789012",
        "elb": {
          "targetGroupArn": "arn:aws:elasticloadbalancing:region:123456789012:targetgroup/my-target-group/6d0ecf831eec9f09"
        },
        "path": "/",
        "resourcePath": "/{proxy+}",
        "httpMethod": "GET",
        "apiId": "1234567890",
        "protocol": "HTTP/1.1"
      }
    }';

        $payload = json_decode($payload, true);

        $handler = new ApiGateway($payload);
        $this->assertFalse($handler->canHandle());
    }

    /** @test */
    public function handler_creates_a_valid_request_object()
    {
        $this->markTestIncomplete('should refactor out into helper, and test');
    }

    /** @test */
    public function converts_a_response_object()
    {
        $kernel = \Mockery::mock('Illuminate\Foundation\Http\Kernel');

        $this->markTestIncomplete();
    }

    /** @test */
    public function converts_a_json_response_object()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function handles_payload()
    {
        $this->markTestIncomplete('should test the returned response that comes out of the mock is sent up');
    }

    /** @test */
    public function it_coverts_headers_json_to_a_server_array_of_headers()
    {
        $payload = json_decode($this->validJson, true);
        $handler = new ApiGateway($payload);

        $this->assertEquals(
            [
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
                'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
                'HTTP_CACHE_CONTROL' => 'max-age=0',
                'HTTP_CLOUDFRONT_FORWARDED_PROTO' => 'https',
                'HTTP_CLOUDFRONT_IS_DESKTOP_VIEWER' => 'true',
                'HTTP_CLOUDFRONT_IS_MOBILE_VIEWER' => 'false',
                'HTTP_CLOUDFRONT_IS_SMARTTV_VIEWER' => 'false',
                'HTTP_CLOUDFRONT_IS_TABLET_VIEWER' => 'false',
                'HTTP_CLOUDFRONT_VIEWER_COUNTRY' => 'US',
                'HTTP_HOST' => '1234567890.execute-api.us-east-1.amazonaws.com',
                'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
                'HTTP_USER_AGENT' => 'Custom User Agent String',
                'HTTP_VIA' => '1.1 08f323deadbeefa7af34d5feb414ce27.cloudfront.net (CloudFront)',
                'HTTP_X_AMZ_CF_ID' => 'cDehVQoZnx43VYQb9j2-nvCh-9z396Uhbp027Y2JvkCPNLmGJHqlaA==',
                'HTTP_X_FORWARDED_FOR' => '127.0.0.1, 127.0.0.2',
                'HTTP_X_FORWARDED_PORT' => '443',
                'HTTP_X_FORWARDED_PROTO' => 'https',
            ],
            $handler->transformHeadersToServerVars($payload['headers'])
        );
    }

    /** @test */
    public function it_coverts_headers_json_to_a_server_array_of_headers_not_prefixing_content_type()
    {
        $payload = json_decode($this->validJson, true);
        $handler = new ApiGateway($payload);
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->assertEquals(
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',

            ],
            $handler->transformHeadersToServerVars($headers)
        );
    }

    /** @test */
    public function it_handles_non_base64_encoded_body()
    {
        $payload = [
            'body' => 'I am a sample body',
            'isBase64Encoded' => false,
        ];
        $handler = new ApiGateway($payload);

        $this->assertEquals('I am a sample body', $handler->getBodyFromPayload());
    }

    /** @test */
    public function it_handles_a_base64_encoded_body()
    {
        $payload = [
            'body' => 'SSBhbSBhIHNhbXBsZSBib2R5IHRoYXQgaXMgYmFzZTY0IGVuY29kZWQ=',
            'isBase64Encoded' => true,
        ];
        $handler = new ApiGateway($payload);

        $this->assertEquals('I am a sample body that is base64 encoded', $handler->getBodyFromPayload());
    }

    /**
     * @test
     * @dataProvider prepareUrlProvider
     */
    public function it_correctly_converts_request_uri_to_parsable_uri($path, $expectation)
    {
        $container = \Mockery::mock('Illuminate\Container\Container');
        $container->shouldReceive('make')->once()->with('config')
            ->andReturn($config = \Mockery::mock('Illuminate\Config\Repository'));
        $config->shouldReceive('get')->once()->with('app.url')->andReturn('http://localhost');

        $handler = new ApiGateway([
            'path' => $path,
        ]);

        $this->assertEquals($expectation, $handler->prepareUrlForRequest($container));
    }

    public function prepareUrlProvider()
    {
        return [
            ['/', 'http://localhost'],
            ['/test', 'http://localhost/test'],
            ['/test/', 'http://localhost/test'],
            ['test/', 'http://localhost/test'],
            ['test', 'http://localhost/test'],
        ];
    }

    /** @test */
    public function it_should_pass_through_kernel_terminate_and_return()
    {
        $payload = json_decode($this->validJson, true);
        $handler = \Mockery::mock('Intouch\LaravelAwsLambda\Handlers\ApiGateway')->makePartial();
        $handler->shouldReceive('createRequest')->once()->andReturn('request');

        $container = \Mockery::mock('Illuminate\Container\Container');
        $container->shouldReceive('make')->once()->with('Illuminate\Contracts\Http\Kernel')
            ->andReturn($kernel = \Mockery::mock('Illuminate\Foundation\Http\Kernel'));

        $kernel->shouldReceive('handle')->once()->with('request')
            ->andReturn($response = \Mockery::mock('Illuminate\Http\Response'));
        $kernel->shouldReceive('terminate')->once()->withArgs(['request', $response]);

        $response->shouldReceive('getContent')->andReturn('Return body');
        $response->headers = \Mockery::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('allPreserveCase')->andReturn(['Content-Type'=>'application/json']);
        $response->shouldReceive('getContent')->andReturn('Return body');
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $container->shouldReceive('make')->once()->with('config')
            ->andReturn($config = \Mockery::mock('Illuminate\Config\Repository'));
        $config->shouldReceive('get')->once()->with('app.url')->andReturn('http://localhost');

        $response = $handler->handle($container);

        $this->assertJsonStringEqualsJsonString(
            '{"body":"Return body","isBase64Encoded":false,"multiValueHeaders":{"Content-Type":"application\/json"},"statusCode":200}',
            $response
        );
    }
}
