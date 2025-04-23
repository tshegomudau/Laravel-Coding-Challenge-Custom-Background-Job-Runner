<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomJob;
use Illuminate\Support\Facades\Log;

class ProcessPendingJobs extends Command
{
    protected $signature = 'jobs:process';

    protected $description = 'Process the next pending custom job';

    public function handle()
    {
        $job = CustomJob::where('status', 0)
            ->where(function ($q) {
                $q->whereNull('run_at')
                  ->orWhere('run_at', '<=', now());
            })
            ->orderBy('priority')
            ->orderBy('created_at')
            ->first();

        if (!$job) {
            $this->info('No pending jobs.');
            return 0;
        }

        
        $runJobPath = base_path('RunJob.php');
        $this->info("Dispatching Job ID: {$job->id}");

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows: 
            $output = [];
            $returnVar = 0;
            exec("php \"{$runJobPath}\" {$job->id}", $output, $returnVar);
            $this->info("Exec output: " . implode("\n", $output));
        } else {
            // Unix-like
            exec("php \"{$runJobPath}\" {$job->id} > /dev/null 2>&1 &");
        }

        return 0;
    }
}
