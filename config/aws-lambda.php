<?php

return [
    'handlers' => [
        \Intouch\LaravelAwsLambda\Handlers\Sqs::class,
        \Intouch\LaravelAwsLambda\Handlers\Artisan::class,
        \Intouch\LaravelAwsLambda\Handlers\ApiGateway::class,
    ],
];
