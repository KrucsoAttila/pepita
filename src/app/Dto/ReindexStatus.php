<?php
namespace App\Dto;

use App\Models\ReindexJob;

class ReindexStatus
{
    public string $jobId;
    public string $status;
    public bool $recreate;
    public ?string $error;
    public ?string $startedAt;
    public ?string $finishedAt;
    public string $createdAt;
    public string $updatedAt;

    public static function fromJob(ReindexJob $job): self
    {
        $self = new self();
        $self->jobId = $job->id;
        $self->status = $job->status;
        $self->recreate = (bool) $job->recreate;
        $self->error = $job->error;
        $self->startedAt = optional($job->started_at)?->toISOString();
        $self->finishedAt = optional($job->finished_at)?->toISOString();
        $self->createdAt = $job->created_at->toISOString();
        $self->updatedAt = $job->updated_at->toISOString();
        return $self;
    }
}
