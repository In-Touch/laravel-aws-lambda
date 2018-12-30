<?php

namespace Intouch\LaravelAwsLambda\Handlers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Response;
use Intouch\LaravelAwsLambda\Contracts\Handler;

class ApiGateway extends Handler
{
    /**
     * @var Container
     */
    protected $app;

    public function canHandle()
    {
        if (
            array_key_exists('body', $this->payload) &&
            array_key_exists('path', $this->payload) &&
            array_key_exists('headers', $this->payload) &&
            array_key_exists('requestContext', $this->payload) &&
            !array_key_exists('elb', $this->payload['requestContext'])
        ) {
            return true;
        }

        return false;
    }

    public function handle(Container $app)
    {
        $this->app = $app;

        $uri = $this->prepareUrlForRequest($this->payload['path']);

        $request = Request::create(
            $uri, $this->payload['httpMethod'],
            $this->payload['queryStringParameters'] !== null ? $this->payload['queryStringParameters'] : [],
            [], [], $this->transformHeadersToServerVars($this->payload['headers']),
            $this->getBodyFromPayload()
        );

        $response = $this->runThroughKernel($request);

        return $this->prepareResponse($response);
    }

    public function getBodyFromPayload()
    {
        if ($this->payload['isBase64Encoded'] === true) {
            return base64_decode($this->payload['body']);
        }

        return $this->payload['body'];
    }

    public function runThroughKernel($request)
    {
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');

        $response = $kernel->handle($request);

        $kernel->terminate($request, $response);

        return $response;
    }

    public function prepareResponse(Response $response)
    {
        $payload = [];

        $payload['body'] = $response->getContent();
        $payload['isBase64Encoded'] = false;
        $payload['multiValueHeaders'] = $response->headers->allPreserveCase();
        $payload['statusCode'] = $response->getStatusCode();

        return json_encode($payload);
    }

    /**
     * Transform headers array to array of $_SERVER vars with HTTP_* format.
     *
     * @param  array $headers
     * @return array
     */
    public function transformHeadersToServerVars(array $headers)
    {
        $server = [];
        $prefix = 'HTTP_';

        foreach ($headers as $name => $value) {
            $name = strtr(strtoupper($name), '-', '_');

            if (!starts_with($name, $prefix) && $name != 'CONTENT_TYPE') {
                $name = $prefix . $name;
            }

            $server[$name] = $value;
        }

        return $server;
    }

    /**
     * Turn the given URI into a fully qualified URL.
     *
     * @param  string $uri
     * @return string
     */
    public function prepareUrlForRequest($uri)
    {
        if (Str::startsWith($uri, '/')) {
            $uri = substr($uri, 1);
        }

        if (!Str::startsWith($uri, 'http')) {
            $uri = config('app.url') . '/' . $uri;
        }

        return trim($uri, '/');
    }
}
