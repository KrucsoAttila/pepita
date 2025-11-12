<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Contracts\ProductIndexService;

class ProductsInitIndex extends Command
{
    protected $signature = 'products:es-init {--recreate}';
    protected $description = 'Create/Update Elasticsearch index & import products via Scout';

    public function handle(): int
    {
        $recreate = (bool) $this->option('recreate');

        $this->info($recreate ? 'Recreating index…' : 'Ensuring index exists…');

        $indexer = app(ProductIndexService::class);
        $indexer->initIndex($recreate);

        $this->info('Importing products via Scout…');
        $indexer->importAll();

        $this->info('Done.');
        return self::SUCCESS;
    }
}
