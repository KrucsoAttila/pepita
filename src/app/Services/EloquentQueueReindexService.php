<?php
namespace App\Services;

use App\Jobs\ProductReindexJob;
use App\Models\ReindexJob;
use App\Services\Contracts\ReindexService;
use Illuminate\Support\Facades\DB;

class EloquentQueueReindexService implements ReindexService
{
    public function dispatchReindex(bool $recreate = false): ReindexJob
    {
        return DB::transaction(function () use ($recreate) {
            $job = ReindexJob::create([
                'status' => 'pending',
                'recreate' => $recreate,
            ]);
            ProductReindexJob::dispatch($job->id, $recreate);
            return $job;
        });
    }
}
