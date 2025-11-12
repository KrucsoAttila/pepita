<?php
namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class ProductView
{
    public string $id;
    public string $title;
    public string $brand;
    public string $categoryId;
    public float  $price;
    public string $currency;
    public int    $stock;
    public string $sellerId;
    public float  $rating;
    public int    $popularity;
    public array  $attributes = [];
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    public static function fromEs(array $hit): self
    {
        $s = $hit['_source'] ?? [];
        $v = new self();
        $v->id = (string)($s['id'] ?? $hit['_id'] ?? '');
        $v->title = (string)($s['title'] ?? '');
        $v->brand = (string)($s['brand'] ?? '');
        $v->categoryId = (string)($s['category_id'] ?? '');
        $v->price = (float) ($s['price'] ?? 0);
        $v->currency = (string)($s['currency'] ?? 'HUF');
        $v->stock = (int)   ($s['stock'] ?? 0);
        $v->sellerId = (string)($s['seller_id'] ?? '');
        $v->rating = (float) ($s['rating'] ?? 0);
        $v->popularity = (int)   ($s['popularity'] ?? 0);
        $v->attributes = (array) ($s['attributes'] ?? []);
        $v->createdAt = isset($s['created_at']) ? (string)$s['created_at'] : null;
        $v->updatedAt = isset($s['updated_at']) ? (string)$s['updated_at'] : null;
        return $v;
    }
}
