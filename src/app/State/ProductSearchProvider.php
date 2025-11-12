<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use App\Dto\ProductSearchQuery;
use App\Dto\ProductView;
use Elastic\Client\ClientBuilderInterface;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ProductSearchProvider implements ProviderInterface
{
    private ProductSearchQuery $dto;
    public function __construct(
        private ClientBuilderInterface $clientBuilder,
        private string $readAlias = 'product_read',
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $this->dto = ProductSearchQuery::fromFilters($context['filters'] ?? []);
        $perPage = $this->dto->itemsPerPage;
        $from = ($this->dto->page - 1) * $perPage;

        try {
            $es = $this->clientBuilder->default();
            if (!$es->indices()->exists(['index'=>$this->readAlias])->asBool()) {
                return new TraversablePaginator(new \ArrayIterator([]), $from, 0, $perPage);
            }
            $resp = $es->search([
                'index' => $this->readAlias,
                'body' => $this->getBody(),
            ]);
        } catch (\Throwable $e) {
            throw new UnprocessableEntityHttpException('Elasticsearch unreachable: ' . $e->getMessage(), $e);
        }

        $hits = Arr::get($resp,'hits.hits',[]);
        $total = isset($resp['hits']['total']['value']) ? (int)$resp['hits']['total']['value'] : (count($hits) + $from);
        $items = array_map(fn(array $h) => ProductView::fromEs($h), $hits);
        return new TraversablePaginator(new \ArrayIterator($items), $from, $total, $perPage);
    }


    private function getBody(): array
    {
        return [
            'from' => ($this->dto->page - 1) * $this->dto->itemsPerPage,
            'size' => $this->dto->itemsPerPage,
            'track_total_hits' => false,
            'query' => $this->getQuery(),
            'sort' => [
                ['popularity' => ['order'=>'desc']],
                ['_score' => ['order'=>'desc']],
                ['id' => ['order'=>'asc']],
            ],
            '_source' => [
                'includes' => ['id','title','brand','price','currency','popularity','rating','attributes','created_at','updated_at','category_id'],
            ],
        ];
    }


    private function getQuery(): array
    {
        return [
            'function_score' => [
                'query' => [
                    'bool' => [
                        'must' => $this->getMust(),
                        'filter' => $this->getFilter()
                    ]
                ],
                'boost_mode' => 'multiply',
                'score_mode' => 'sum',
                'functions' => [[
                    'field_value_factor' => [
                        'field' => 'rating',
                        'factor' => 1.1,
                        'modifier' => 'sqrt',
                        'missing' => 0
                    ]], [
                    'field_value_factor' => [
                        'field' => 'popularity',
                        'factor' => 0.002,
                        'modifier' => 'sqrt',
                        'missing' => 0
                    ]],
                ],
                'max_boost' => 3,
            ],
        ];
    }

    private function getFilter(): array
    {
        $filter = [];
        if ($this->dto->brands) {
            $filter[] = ['terms' => ['brand.keyword' => array_map('mb_strtolower',$this->dto->brands)]];
        }
        if ($this->dto->categoryId) {
            $filter[] = ['term' => ['category_id' => $this->dto->categoryId]];
        }
        if ($this->dto->priceMin !== null || $this->dto->priceMax !== null) {
            $r = [];
            if($this->dto->priceMin !== null) {
                $r['gte'] = (float)$this->dto->priceMin;
            }
            if($this->dto->priceMax !== null) {
                $r['lte'] = (float)$this->dto->priceMax;
            }
            $filter[] = ['range' => ['price' => $r]];
        }
        if ($this->dto->ratingMin !== null || $this->dto->ratingMax !== null) {
            $r = [];
            if ($this->dto->ratingMin !== null) {
                $r['gte'] = (float)$this->dto->ratingMin;
            }
            if ($this->dto->ratingMax !== null) {
                $r['lte'] = (float)$this->dto->ratingMax;
            }
            $filter[] = ['range' => ['rating' => $r]];
        }
        foreach ($this->dto->attr as $k => $v) {
            $filter[] = ['term' => ["attributes.$k" => $v]];
        }

        return $filter;
    }

    private function getMust(): array
    {
        $q = $this->dto->q;
        if ( $q == '') {
            return [['match_all' => (object)[]]];
        }
        $len = mb_strlen($q);
        $must = [];
        if ($len <= 2) {
            $must[] = [
                'multi_match' => [
                    'query' => $q,
                    'type' => 'bool_prefix',
                    'fields' => [
                        'title.s',
                        'title.s._2gram',
                        'title.s._3gram',
                    ],
                ],
            ];
        } else {
            $must[] = [
                'multi_match' => [
                    'query' => $q,
                    'type' => 'best_fields',
                    'fields' => ['title^2','title.s','brand','brand.keyword'],
                    'operator' => 'OR',
                    'fuzziness' => 'AUTO:1,5',
                    'prefix_length' => 1,
                ],
            ];
        }
        return $must;
    }
}
