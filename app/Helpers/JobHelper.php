<?php

use App\Models\CustomJob;
use Symfony\Component\Process\Process;

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($class, $method, $params = [], $options = [])
    {
        $job = CustomJob::create([
            'class' => $class,
            'method' => $method,
            'params' => $params,
            'chain' => $options['chain'] ?? [],
            'priority' => $options['priority'] ?? 5,
            'run_at' => now()->addSeconds($options['delay'] ?? 0),
        ]);

        $cmd = ['php', base_path('run-job.php'), (string)$job->id];

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            pclose(popen('start /B ' . implode(' ', $cmd), 'r'));
        } else {
            (new Process($cmd))->start();
        }
    }
}
