<?php

namespace App\Jobs;
use Illuminate\Support\Facades\Log;


use App\Exceptions\RetryException;

class TestJob
{
    public function handle($jb)
    {
        if ($jb === 'maybe-fail' && rand(0, 1)) {
            throw new \App\Exceptions\RetryException("Simulated retryable failure.");
        }

        Log::info("Successfully ran job with param: $jb");
    }

}
