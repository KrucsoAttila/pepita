<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Seller;
use Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }
        if (Seller::count() === 0) {
            $this->call(SellerSeeder::class);
        }

        Product::factory(100)->create();

         $samples = [
            ['Apple iPhone 13 Pro', 'Apple', 399999.00, 'black'],
            ['Apple iPhone 14',     'Apple', 459999.00, 'blue'],
            ['Apple iPhone 15 Pro', 'Apple', 599999.00, 'titanium'],
            ['Apple iPhone SE',     'Apple', 199999.00, 'white'],
        ];
        foreach ($samples as [$title,$brand,$price,$color]) {
            Product::create([
                'id' => (string) Str::uuid(),
                'title' => $title,
                'brand' => $brand,
                'category_id' => (string) Str::uuid(),
                'price' => $price,
                'currency' => 'HUF',
                'stock' => 10,
                'seller_id' => (string) Str::uuid(),
                'rating' => 4.6,
                'popularity' => rand(100, 800),
                'attributes' => ['color' => $color, 'memory' => '128GB'],
            ]);
        }
    }
}
