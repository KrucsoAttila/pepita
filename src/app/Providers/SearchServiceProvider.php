<?php
namespace App\Providers;

use App\Services\Contracts\ProductIndexService;
use App\Services\ElasticsearchProductIndexer;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductIndexService::class, ElasticsearchProductIndexer::class);
    }
}
