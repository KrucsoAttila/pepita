<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProductReindexJob;
use App\Models\ReindexJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;

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

    public function reindexStatus(string $id): JsonResponse
    {
        $rec = ReindexJob::findOrFail($id);

        return response()->json([
            'job_id' => $rec->id,
            'status' => $rec->status,
            'recreate' => (bool) $rec->recreate,
            'error' => $rec->error,
            'started_at' => optional($rec->started_at)?->toISOString(),
            'finished_at' => optional($rec->finished_at)?->toISOString(),
            'created_at' => $rec->created_at->toISOString(),
            'updated_at' => $rec->updated_at->toISOString(),
        ]);
    }
}
