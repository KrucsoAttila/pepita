<?php
// File: tests/Feature/ProductReindexJobTest.php
namespace Tests\Feature;

use App\Jobs\ProductReindexJob;
use App\Models\ReindexJob;
use App\Services\Contracts\ProductIndexService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReindexJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_marks_completed_on_success(): void
    {
        $rec = ReindexJob::create(['status' => 'pending', 'recreate' => true]);

        $this->app->bind(ProductIndexService::class, function () {
            return new class implements ProductIndexService {
                public function reindex(bool $recreate = false): void {}
                public function initIndex(bool $recreate = false): void {}
                public function importAll(): void {}
            };
        });

        $job = new ProductReindexJob($rec->id, true);
        $this->app->call([$job, 'handle']);

        $this->assertDatabaseHas('reindex_jobs', [
            'id' => $rec->id,
            'status' => 'completed',
            'recreate' => 1,
        ]);
    }

    public function test_job_marks_failed_on_exception(): void
    {
        $rec = ReindexJob::create(['status' => 'pending', 'recreate' => false]);

        $this->app->bind(ProductIndexService::class, function () {
            return new class implements ProductIndexService {
                public function reindex(bool $recreate = false): void { throw new \RuntimeException('boom'); }
                public function initIndex(bool $recreate = false): void {}
                public function importAll(): void {}
            };
        });

        $job = new ProductReindexJob($rec->id, false);

        try {
            $this->app->call([$job, 'handle']);
            $this->fail('Exception not thrown');
        } catch (\RuntimeException $e) {
            $this->assertSame('boom', $e->getMessage());
        }

        $this->assertDatabaseHas('reindex_jobs', [
            'id' => $rec->id,
            'status' => 'failed',
        ]);
    }
}
