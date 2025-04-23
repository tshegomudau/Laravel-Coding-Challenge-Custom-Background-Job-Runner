<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomJob;

class JobController extends Controller
{
    //
    public function index(Request $request)
    {
        return CustomJob::orderBy('created_at', 'desc')->paginate(10);
    }

    public function cancel($id)
    {
        $job = CustomJob::findOrFail($id);
        if (in_array($job->status, ['0', '1'])) {
            $job->update(['status' => '4']);
        }
        return response()->json(['success' => true]);
    }

    public function test(Request $request)
    {
        $type = $request->input('type');

        switch ($type) {
            case 'basic':
                runBackgroundJob(\App\Jobs\TestJob::class, 'handle', ['basic']);
                break;

            case 'delayed':
                runBackgroundJob(\App\Jobs\TestJob::class, 'handle', ['delayed'], ['delay' => 5]);
                break;

            case 'chained':
                runBackgroundJob(\App\Jobs\TestJob::class, 'handle', ['first'], [
                    'chain' => [
                        ['class' => \App\Jobs\TestJob::class, 'method' => 'handle', 'params' => ['second']],
                    ],
                ]);
                break;

            case 'retry':
                runBackgroundJob(\App\Jobs\TestJob::class, 'handle', ['maybe-fail']);
                break;

            case 'disallowed':
                runBackgroundJob(\App\Jobs\TestJob::class, 'update');
                break;

            default:
                return response()->json(['message' => 'Unknown test type'], 400);
        }

        return response()->json(['message' => "Triggered $type job successfully"]);
    }
    
}
