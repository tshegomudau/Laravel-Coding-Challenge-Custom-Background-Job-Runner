<?php

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CustomJob;

define('STATUS_PENDING', 0);
define('STATUS_RUNNING', 1);
define('STATUS_COMPLETED', 2);
define('STATUS_FAILED', 3);


$id = $argv[1] ?? null;

if (!$id) {
    echo "No job ID passed.\n";
    exit(1);
}

$job = CustomJob::find($id);


if (!$job || $job->status !== STATUS_PENDING) {
    echo "Invalid or already processed job.\n";
    exit(1);
}

// Mark job as running
$job->update([
    'status' => STATUS_RUNNING,
    'started_at' => now(),
]);

try {
    $class = $job->class;
    $method = $job->method;
    $params = $job->params ?? [];

    $allowed = config('jobs.allowed');

    if (!isset($allowed[$class]) || !in_array($method, $allowed[$class])) {
        throw new \Exception("Unauthorized job.");
    }

    $instance = new $class();
    call_user_func_array([$instance, $method], $params);

    $job->update([
        'status' => STATUS_COMPLETED,
        'finished_at' => now(),
    ]);

    if (!empty($job->chain)) {
        foreach ($job->chain as $chained) {
            CustomJob::create([
                'class' => $chained['class'],
                'method' => $chained['method'],
                'params' => $chained['params'] ?? [],
                'status' => STATUS_PENDING,
                'priority' => $chained['priority'] ?? 5,
                'run_at' => now(), 
            ]);
        }
    }

} catch (\Throwable $e) {
    $shouldRetry = get_class($e) === App\Exceptions\RetryException::class;
    $retries = $job->retries + 1;

    $job->update([
        'retries' => $retries,
        'status' => ($shouldRetry && $retries < 3) ? STATUS_PENDING : STATUS_FAILED,
        'error' => $e->getMessage(),
    ]);
}
