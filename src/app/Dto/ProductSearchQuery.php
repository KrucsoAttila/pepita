<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductSearchQuery
{
    #[Assert\Type('string')]
    public ?string $q = null;

    #[Assert\Positive]
    public int $page = 1;

    #[Assert\Range(min: 1, max: 50)]
    public int $itemsPerPage = 24;

    /** @var list<string> */
    #[Assert\All([new Assert\Type('string')])]
    public array $brands = [];

    #[Assert\Type('string')]
    public ?string $categoryId = null;

    #[Assert\Type('numeric')]
    public mixed $priceMin = null;

    #[Assert\Type('numeric')]
    public mixed $priceMax = null;

    #[Assert\Type('numeric')]
    public mixed $ratingMin = null;

    #[Assert\Type('numeric')]
    public mixed $ratingMax = null;

    /** @var array<string, scalar|null> */
    #[Assert\Type('array')]
    public array $attr = [];

    public static function fromFilters(array $f): self
    {
        $self = new self();

        $self->q = trim((string)($f['q'] ?? '')) ?: null;
        $self->page = max(1, (int)($f['page'] ?? 1));
        $self->itemsPerPage = min(50, max(1, (int)($f['itemsPerPage'] ?? 24)));

        $brands = $f['brand'] ?? $f['brands'] ?? [];
        $self->brands = array_values(is_array($brands) ? array_filter($brands, fn($v)=>$v!=='' && $v!==null) : []);

        $self->categoryId = ($f['categoryId'] ?? $f['category_id'] ?? null) ?: null;

        $price = (array)($f['price'] ?? []);
        $self->priceMin = $price['min'] ?? null;
        $self->priceMax = $price['max'] ?? null;

        $rating = (array)($f['rating'] ?? []);
        $self->ratingMin = $rating['min'] ?? null;
        $self->ratingMax = $rating['max'] ?? null;

        $attrs = (array)($f['attr'] ?? []);
        $self->attr = array_filter(
            array_map(fn($k,$v) => $v, array_keys($attrs), $attrs),
            fn($v) => $v !== '' && $v !== null
        );

        return $self;
    }
}
