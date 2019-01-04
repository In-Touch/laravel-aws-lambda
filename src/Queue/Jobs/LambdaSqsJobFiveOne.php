<?php
/**
 * Created by PhpStorm.
 * User: lwaite
 * Date: 2019-01-04
 * Time: 16:40
 */

namespace Intouch\LaravelAwsLambda\Queue\Jobs;

/**
 * A Laravel 5.1 and 5.2 compatible Job.
 *
 * Class LambdaSqsJobFiveOne
 * @package Intouch\LaravelAwsLambda\Queue\Jobs
 */
class LambdaSqsJobFiveOne extends LambdaSqsJob
{
    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $this->resolveAndFire(json_decode($this->getRawBody(), true));
    }
}