<?php
namespace App\Services\Contracts;

use App\Models\ReindexJob;

interface ReindexService
{
    public function dispatchReindex(bool $recreate = false): ReindexJob;
}
