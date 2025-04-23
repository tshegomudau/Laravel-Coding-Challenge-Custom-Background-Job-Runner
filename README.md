# Laravel Custom Background Job Runner

A custom, database-backed background job runner built in Laravel, designed to execute PHP jobs independently from Laravel's built-in queue system.

##  Features

- Lightweight background job execution using `exec` or `Symfony\Process`
- Retry support for specific exceptions (e.g., `RetryException`)
- Job chaining
- Delayed execution and prioritization
- Secure method/class whitelisting
- Works without Laravel’s built-in queue worker


## Setup Instructions

### 1. Clone and Install Dependencies
git clone https://github.com/tshegomudau/Laravel-Coding-Challenge-Custom-Background-Job-Runner.git
cd Laravel-Coding-Challenge-Custom-Background-Job-Runner
composer install
cp .env.example .env
php artisan key:generate

php artisan migrate


## 2. Register Scheduler Command
## In App\Console\Kernel.php: Add
protected function schedule(Schedule $schedule)
{
    $schedule->command('jobs:process')->everyMinute();
}

## Then run Laravel’s scheduler (via cron or manually):
php artisan schedule:work
php artisan jobs:process 

## Usage
### Use runBackgroundJob helper:
runBackgroundJob(
    \App\Jobs\TestJob::class,
    'handle',
    ['example-param'],                 // Parameters for the job method
    [
        'delay' => 5,                  // Delay in seconds (optional)
        'priority' => 3,              // Lower = higher priority (optional)
        'chain' => [                  // Chained jobs (optional)
            [
                'class' => \App\Jobs\TestJob::class,
                'method' => 'handle',
                'params' => ['next-task'],
            ],
        ],
    ]
);

## You can call this from controllers, services, etc.
### Example Controller
public function test(Request $request)
{
    runBackgroundJob(\App\Jobs\TestJob::class, 'handle', ['test-param']);

    return response()->json(['message' => 'Job dispatched!']);
}

## Test Interface
### The project includes a simple web-based test panel for triggering background jobs manually. It can be accessed at:
#basepath/test-jobs

## Button	Description
### Run Basic Job	Immediately runs a simple background job.
### Run Delayed Job (5s)	Schedules a job to run after a 5-second delay.
### Run Chained Job	Runs a job and chains a follow-up job.
### Run Retry Job (fails randomly)	Simulates a flaky job that may retry on failure.
### Run Disallowed Job	Attempts to run a job not whitelisted (should fail).
## Each button triggers runBackgroundJob() under the hood with varying configurations (delay, chaining, retries, etc.).

## Retry Logic
### Throw a custom App\Exceptions\RetryException in your job to indicate retryable failure:
### Retry attempts are capped at 3 by default.
### You can modify this in the RunJob.php script logic.

## Optional Features
### Priority Queueing
#### Jobs are selected and dispatched by priority (lower = higher priority). Update jobs to set a priority value between 1–10 (default: 5).
### Job Chaining
#### Jobs can schedule dependent jobs using the chain option. These chained jobs are stored in the same custom_jobs table and processed automatically.
