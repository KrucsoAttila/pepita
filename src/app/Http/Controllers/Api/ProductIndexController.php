<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProductReindexJob;
use App\Models\ReindexJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use App\Dto\ReindexStatus;
use App\Services\Contracts\ReindexService;

class ProductIndexController extends Controller
{
    public function __construct(private readonly ReindexService $reindex) {}

    public function reindexAsync(Request $request): JsonResponse
    {
        $job = $this->reindex->dispatchReindex($request->boolean('recreate', false));
        return response()->json([
            'status' => 'accepted',
            'job_id' => $job->id,
        ], 202);
    }

    public function reindexStatus(ReindexJob $job): JsonResponse
    {
        return response()->json(ReindexStatus::fromJob($job));
    }
}
