<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientInterface;
use Elastic\Client\ClientBuilderInterface;

class ElasticsearchServiceProvider extends ServiceProvider
{
     public function register(): void
    {
        $this->app->singleton(ClientInterface::class, function ($app) {
            /** @var ClientBuilderInterface $builder */
            $builder = $app->make(ClientBuilderInterface::class);
            return $builder->default();
        });
        $this->app->alias(ClientInterface::class, 'elastic.client');
    }
}
