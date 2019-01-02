# Laravel AWS Lambda

[![Latest Version on Packagist](https://img.shields.io/packagist/v/intouch/laravel-aws-lambda.svg?style=flat-square)](https://packagist.org/packages/intouch/laravel-aws-lambda)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/In-Touch/laravel-aws-lambda/master.svg?style=flat-square)](https://travis-ci.org/In-Touch/laravel-aws-lambda)
[![Coveralls](https://img.shields.io/coveralls/github/In-Touch/laravel-aws-lambda/master.svg?style=flat-square)](https://coveralls.io/github/In-Touch/laravel-aws-lambda)
[![StyleCI](https://styleci.io/repos/163447633/shield)](https://styleci.io/repos/163447633)
[![Total Downloads](https://img.shields.io/packagist/dt/intouch/laravel-aws-lambda.svg?style=flat-square)](https://packagist.org/packages/intouch/laravel-aws-lambda)

This package adds support to run Laravel in AWS Lambda.

## Contents

- [Installation](#installation)
- [Usage](#usage)
    - [API Gateway Usage](#api-gateway-usage)
    - [SQS Usage](#sqs-usage)
    - [Running an Artisan Command](#running-an-artisan-command)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

1. First, install the package via composer:

```bash
composer require intouch/laravel-aws-lambda
```

2. On versions of Laravel less than 5.5, you'll want to register the service provider.
Add `Intouch\LaravelAwsLambda\LambdaServiceProvider::class,` to the `providers` array
in `config/app.php`.

3. Install the provided files in your application:
```bash
php artisan vendor:publish --provider="Intouch\LaravelAwsLambda\LambdaServiceProvider"
```

### Advanced Installation Notes
* The ServiceProvider is only required when publishing the files from the package. Feel
free to either remove, or wrap in an environment check not not load in production.

## Usage

You'll need to run this in a Lambda runtime which supports executing php. See
[aws-lambda-runtime-php](https://github.com/lukewaite/aws-lambda-php-runtime), which
is the runtime we use for testing and development of the package.

Once the package is installed, upload your built Laravel application to AWS Lambda as
a function. Specify the Handler as `handler.handler`; this will load the published
`handler.php` which was provided at the root of your application, and then invoke the
`handler()` entrypoint into the package, which will bootstrap your application.

Currently the package supports events from two AWS data sources:
* API Gateway
* SQS

Additionally, you may invoke the lambda (manually, or via CloudWatch events) with a
custom JSON payload specifying an artisan command to be run.

#### API Gateway Usage
Register a route (single route, or `{proxy+}`) which is configured as a `LAMBDA_PROXY`
route passing the request through to your lambda function.

The Laravel AWS Lambda package will pass the API Gateway request through the HTTP
Kernel, thus following your normal application routing. The response object will then
be converted to a valid `LAMBDA_PROXY` response, and returned to the caller.

#### SQS Usage
Configure an SQS queue to deliver messages to the AWS Lambda function. There is no
need to configure or run a listener; the lambda will be invoked on demand when a
message is delivered to the queue. It is recommended that you configure the batch
size to `1`.

Publish a valid Laravel queue message via the Laravel SQS Queue driver.

The Lambda will be invoked, the package will process the received message, and
instantiate your job class as per normal.

##### Notes about running SQS jobs via AWS Lambda
When running SQS jobs via Lambda, it is Lambda that is responsible for executing the
`DeleteMessage` call against SQS after successful execution. This occurs when the
Lambda execution exits successfully.

If you configure Lambda to invoke with a batch size that is not one, multiple jobs
may be passed through to the Lambda invocation. The Laravel AWS Lambda package
will handle this for you by running each job in series. However, if _any_ job
encounters a failure, _all_ jobs in the batch will be failed and retried by Lambda.

Running multiple jobs per batch is supported, and should permit higher throughput,
but you should be aware of the possible consequences, and ensure your application
is designed to handle multiple executions of already-run-jobs.

#### Running an Artisan command
You can pass a custom payload through to the Lambda to run an Artisan command:
```json
{
  "command": "inspire"
}
```

The above sample payload will invoke the `inspire` command. You can use custom
payloads such as this either via manual invocation during process automations,
or as scheduled executions via CloudWatch Events in place of commands you would
typically run via the Artisan Scheduler.

## Extending
The Larvel AWS Lambda package reads an array of `Handlers` from configuration
specified in `config/aws-lambda.php`. You may add your own custom handlers to
this list, if desired.

Handlers are passed the `$payload` of the lambda invocation when they are being
created.

Next, in a loop, each Handler is evaluated by calling the `canHandle()` method.
Your handler should examine the payload, and determine if this is a job that
should be handled by this handler.

When a handler returns `true` to the `canHandle()` check, it's `handle()` method
is called. You may type-hint dependencies in the `handle()` method and they
will be injected by the IoC while calling the method, similar to how Controllers,
Jobs, and other invocations work in Laravel.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email lwaite@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Intouch Insight](https://www.intouchinsight.com)
- [Luke Waite](https://github.com/lukewaite)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
