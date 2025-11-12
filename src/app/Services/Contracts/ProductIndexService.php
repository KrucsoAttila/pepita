<?php
namespace App\Services\Contracts;

interface ProductIndexService
{
    public function reindex(bool $recreate = false): void;

    public function initIndex(bool $recreate = false): void;

    public function importAll(): void;
}
