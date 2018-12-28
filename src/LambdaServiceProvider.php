<?php

namespace Intouch\LaravelAwsLambda;

use Illuminate\Support\ServiceProvider;

class LambdaServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([__DIR__.'/../config/aws-lambda.php' => config_path('aws-lambda.php')], 'config');
        $this->publishes([__DIR__.'/../handler/handler.php' => base_path('handler.php')], 'handler');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'aws-lambda');

    }

    protected function configPath()
    {
        return __DIR__.'/../config/aws-lambda.php';
    }
}
