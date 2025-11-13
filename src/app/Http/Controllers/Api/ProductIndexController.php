<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProductReindexJob;
use App\Models\ReindexJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use App\Dto\ReindexStatus;

class ProductIndexController extends Controller
{
    public function reindexAsync(Request $request): JsonResponse
    {
        $recreate = $request->boolean('recreate', false);

        $rec = ReindexJob::create([
            'status' => 'pending',
            'recreate' => $recreate,
        ]);

        Queue::push(new ProductReindexJob($rec->id, $recreate));

        return response()->json([
            'status' => 'accepted',
            'job_id' => $rec->id,
        ], 202);
    }

    public function reindexStatus(ReindexJob $job): JsonResponse
    {
        return response()->json(ReindexStatus::fromJob($job));
    }
}
