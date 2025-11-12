<?php
namespace App\Jobs;

use App\Models\ReindexJob;
use App\Services\Contracts\ProductIndexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProductReindexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $jobId;
    public bool $recreate;

    public function __construct(string $jobId, bool $recreate)
    {
        $this->jobId = $jobId;
        $this->recreate = $recreate;
    }

    public function handle(ProductIndexService $indexer): void
    {
        $record = ReindexJob::findOrFail($this->jobId);
        $record->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $indexer->reindex($this->recreate);
            $record->update([
                'status' => 'completed',
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            $record->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'finished_at' => now(),
            ]);
            throw $e;
        }
    }
}
