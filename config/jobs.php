<?php

return [
    'allowed' => [
        App\Jobs\TestJob::class => ['handle'],
    ],
    'retry' => [
        'max_attempts' => 3,
        'delay' => 5, 
    ],
];