<?php

/**
 * Require The Auto Loader.
 *
 * Composer provides a convenient, automatically generated class loader
 * for our application. We just need to utilize it! We'll require it
 * into the script here so that we do not have to worry about the
 * loading of any our classes "manually". Feels great to relax.
 */
require __DIR__ . '/../../../../bootstrap/autoload.php';

/**
 * Turn On The Lights.
 *
 * We need to illuminate PHP development, so let us turn on the lights.
 * This bootstraps the framework and gets it ready for use, then it
 * will load up this application so that we can run it and send
 * the responses back to the browser and delight our users.
 */
$app = require __DIR__ . '/../../../../bootstrap/app.php';

/*
 * Configure storage.
 *
 * Lambda instantiates tasks onto a read-only filesystem, but provides 500MB
 * of non-persistent disk space at /tmp, so we reconfigure our application
 * to use /tmp/laravel for our storage path.
 */
$app->useStoragePath('/tmp/laravel');

/**
 * Create working directories.
 *
 * /tmp storage is not guaranteed to be persistent, but neither is it
 * guaranteed to be clean (from previous runs) on the initialization of a
 * lambda. We check to ensure all of our paths that are expected to exist
 * as a subset of storage_path() do in fact exist.
 */
$paths = [
    '/tmp/laravel/framework/sessions',
    '/tmp/laravel/framework/cache',
    '/tmp/laravel/framework/views',
    '/tmp/laravel/app',
    '/tmp/laravel/logs',
];
foreach ($paths as $path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

/*
 * Bootstrap the application.
 *
 * Regardless of whether we are going to end up processing a HTTP request
 * from API Gateway, SQS Job, or another type of task, we boostrap the
 * application via the Console Kernel, which is safe to do, and in stock
 * Laravel installations contains the same Bootstrappers as the Http Kernel.
 *
 * We do this after:
 * 1. Overriding storage_path() above with useStoragePath() to ensure that
 *    configurations being bootstrapped respect the correct storage path.
 * 2. Creating the /tmp storage directories, in case a ServiceProvider
 *    attempts to perform some form of "setup" into one of the storage
 *    directories.
 */
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

/**
 * Declare our handler.
 *
 * Finally, after bootstrapping the application and doing all of the
 * required "setup" tasks, we declare our handler function which we can
 * invoke to process events in AWS Lambda, passing in the $app Container
 * which we previously instantiated and bootstrapped.
 *
 * @param $payload
 * @return mixed
 */
function handler($payload)
{
    global $app;

    $handler = new \Intouch\LaravelAwsLambda\Executor($app);

    return $handler->handle($payload);
}
