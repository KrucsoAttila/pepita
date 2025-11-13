<?php
namespace App\Services;

use App\Models\Product;
use App\Services\Contracts\ProductIndexService;
use Elastic\Client\ClientBuilderInterface;
use Elastic\Elasticsearch\ClientInterface;
use Throwable;

class ElasticsearchProductIndexer implements ProductIndexService
{
    private ClientInterface $es;
    private string $index;

    public function __construct(private readonly ClientBuilderInterface $builder)
    {
        $this->es = $this->builder->default();
        $this->index = (string) config('scout.prefix') . (string) env('ELASTICSEARCH_INDEX_PRODUCTS', 'products');
    }

    public function reindex(bool $recreate = false): void
    {
        $this->initIndex($recreate);
        $this->importAll();
    }

    public function initIndex(bool $recreate = false): void
    {
        if ($recreate) {
            $this->es->indices()->delete(['index' => $this->index]);
        }

        $exists = $this->es->indices()->exists(['index' => $this->index])->asBool();
        if ($exists) {
            return;
        }

        $this->es->indices()->create([
            'index' => $this->index,
            'body'  => $this->indexDefinition(),
        ]);
    }

    public function importAll(): void
    {
        Product::query()
            ->orderBy('id')
            ->chunkById(100, static function ($chunk): void {
                /** @var \Illuminate\Support\Collection<int,Product> $chunk */
                $chunk->searchable();
            });
    }

    private function indexDefinition(): array
    {
        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => (int) env('ES_REPLICAS', 0),
                'index' => [
                    'sort.field' => 'popularity',
                    'sort.order' => 'desc',
                    'queries.cache.enabled' => true,
                ],
                'analysis' => [
                    'normalizer' => [
                        'lowercase_normalizer' => [
                            'type' => 'custom',
                            'filter' => ['lowercase'],
                        ],
                    ],
                    'analyzer' => [
                        'edge_ngram_analyzer' => [
                            'tokenizer' => 'edge_ngram_tokenizer',
                            'filter' => ['lowercase'],
                        ],
                    ],
                    'tokenizer' => [
                        'edge_ngram_tokenizer' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 2,
                            'max_gram' => 12,
                            'token_chars' => ['letter','digit'],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'dynamic' => true,
                'properties' => [
                    'popularity' => ['type' => 'integer'],
                    'id' => ['type'=>'keyword'],
                    'title' => [
                        'type'=>'text',
                        'analyzer'=>'standard',
                        'fields'=>[
                            'ngram' => ['type'=>'text','analyzer'=>'edge_ngram_analyzer'],
                            's' => ['type'=>'search_as_you_type'],
                        ],
                    ],
                    'brand' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => ['type' => 'keyword', 'normalizer' => 'lowercase_normalizer'],
                        ],
                    ],
                    'category_id' => ['type'=>'keyword'],
                    'price' => ['type'=>'float'],
                    'currency' => ['type'=>'keyword'],
                    'stock' => ['type'=>'integer'],
                    'seller_id' => ['type'=>'keyword'],
                    'rating' => ['type'=>'float'],
                    'attributes' => ['type'=>'object','dynamic'=>true],
                    'created_at' => ['type'=>'date','format'=>'strict_date_optional_time||epoch_millis'],
                    'updated_at' => ['type'=>'date','format'=>'strict_date_optional_time||epoch_millis'],
                ],
            ],
        ];
    }
}
