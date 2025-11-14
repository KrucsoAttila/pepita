<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\ProductIndexService;
use App\Services\ElasticsearchProductIndexer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Services\Contracts\ReindexService;
use App\Services\EloquentQueueReindexService;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Mapping\LazyLoadingMetadataFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductIndexService::class, ElasticsearchProductIndexer::class);
        $this->app->singleton(ValidatorInterface::class, function () {
            $builder = Validation::createValidatorBuilder();
            if (method_exists($builder, 'enableAttributeMapping')) {
                $builder->enableAttributeMapping();
            } else {
                $builder->setMetadataFactory(
                    new LazyLoadingMetadataFactory(new AttributeLoader())
                );
            }
            return $builder->getValidator();
        });
        $this->app->bind(ReindexService::class, EloquentQueueReindexService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
