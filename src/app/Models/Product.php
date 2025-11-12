<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Scout\Searchable;
use ApiPlatform\Metadata\{ApiResource, GetCollection, Get, Post, Put, Patch, Delete};
use App\State\ProductSearchProvider;
use App\Dto\ProductView;

#[ApiResource(
    operations: [
        new GetCollection(uriTemplate: '/products'),
        new Get(uriTemplate: '/products/{id}', requirements: ['id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}']),
        new Post(uriTemplate: '/products'),
        new Put(uriTemplate: '/products/{id}', requirements: ['id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}']),
        new Patch (uriTemplate: '/products/{id}', requirements: ['id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}']),
        new Delete(uriTemplate: '/products/{id}', requirements: ['id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}']),
        new GetCollection(
            uriTemplate: '/products/search',
            output: ProductView::class,
            provider: ProductSearchProvider::class
        ),
    ],
)]
class Product extends Model
{
    use HasFactory, HasUuids, Searchable;

    protected $fillable = [
        'title','brand','category_id','price','currency','stock',
        'seller_id','rating','popularity','attributes'
    ];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
        'rating' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }


    public function searchableAs(): string
    {
        return config('scout.prefix').(env('ELASTICSEARCH_INDEX_PRODUCTS','products'));
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'title' => (string) $this->title,
            'brand' => (string) $this->brand,
            'category_id' => (string) $this->category_id,
            'price' => (float) $this->price,
            'currency' => (string) $this->currency,
            'stock' => (int) $this->stock,
            'seller_id' => (string) $this->seller_id,
            'rating' => (float) $this->rating,
            'popularity' => (int) $this->popularity,
            'attributes' => (array) ($this->attributes ?? []),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
