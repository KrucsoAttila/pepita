<?php
namespace App\Dto;

use App\Models\ReindexJob;

class ReindexStatus
{
    public static function fromJob(ReindexJob $job): array
    {
        return [
            'job_id' => $job->id,
            'status' => $job->status,
            'recreate' => (bool) $job->recreate,
            'error' => $job->error,
            'started_at' => optional($job->started_at)?->toISOString(),
            'finished_at' => optional($job->finished_at)?->toISOString(),
            'created_at' => $job->created_at->toISOString(),
            'updated_at' => $job->updated_at->toISOString(),
        ];
    }
}
