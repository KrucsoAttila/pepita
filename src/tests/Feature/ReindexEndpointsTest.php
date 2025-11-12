<?php
// File: tests/Feature/ReindexEndpointsTest.php
namespace Tests\Feature;

use App\Jobs\ProductReindexJob;
use App\Models\ReindexJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReindexEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_reindex_dispatches_job_and_creates_record(): void
    {
        Queue::fake();

        $resp = $this->postJson('/api/search/products/reindex', ['recreate' => true]);
        $resp->assertStatus(202)->assertJsonStructure(['status','job_id']);

        $jobId = $resp->json('job_id');

        $this->assertDatabaseHas('reindex_jobs', [
            'id' => $jobId,
            'status' => 'pending',
            'recreate' => 1,
        ]);

        Queue::assertPushed(ProductReindexJob::class, function ($job) use ($jobId) {
            /** @var ProductReindexJob $job */
            return $job->jobId === $jobId && $job->recreate === true;
        });
    }

    public function test_get_reindex_status_returns_payload(): void
    {
        $rec = ReindexJob::factory()->create([
            'status' => 'running',
            'recreate' => false,
        ]);

        $resp = $this->getJson("/api/search/products/reindex/{$rec->id}");
        $resp->assertOk()
            ->assertJson([
                'job_id' => $rec->id,
                'status' => 'running',
                'recreate' => false,
            ]);
    }
}
