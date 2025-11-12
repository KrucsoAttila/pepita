<?php
namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $brands = ['Acme','Globex','Umbrella','Initech','Soylent','Stark','Wayne','Oscorp','Nuka','Aperture','OmniCorp','Canon','Sony','Samsung','Apple','Xiaomi','LG','Bosch','Philips'];
        $currencies = ['HUF','EUR','USD'];
        $colors = ['red','blue','green','black','white','gray','yellow','purple','orange','navy','beige','brown'];
        $sizes = ['XS','S','M','L','XL','XXL', '36','38','40','42','44', 'OneSize'];
        $materials = ['cotton','polyester','wool','leather','steel','plastic','wood','glass','aluminum'];

        $categoryId = Category::inRandomOrder()->value('id') ?? Category::factory()->create()->id;
        $sellerId = Seller::inRandomOrder()->value('id') ?? Seller::factory()->create()->id;

        $brand = $this->faker->randomElement($brands);
        $noun = $this->faker->randomElement(['Phone','Headphones','Backpack','Shoes','Jacket','Mixer','Pan','Lamp','Puzzle','Stroller','Diapers','Monitor','Keyboard','SSD','Drill','Vacuum']);
        $adjective = $this->faker->randomElement(['Pro','Mini','Max','Lite','Ultra','Air','Smart','Eco','Plus']);

        $isWearable = in_array($noun, ['Shoes','Jacket','Backpack']);
        $isElectronics = in_array($noun, ['Phone','Headphones','Monitor','Keyboard','SSD']);
        $isHome = in_array($noun, ['Mixer','Pan','Lamp','Drill','Vacuum']);

        $attrs = [
            'color' => $this->faker->randomElement($colors),
            'size' => $isWearable ? $this->faker->randomElement($sizes) : null,
            'material' => $this->faker->randomElement($materials),
            'warranty' => $isElectronics ? $this->faker->randomElement([12,24,36]) . 'm' : null,
            'power' => $isHome ? $this->faker->randomElement([500,800,1200,1600]) . 'W' : null,
        ];

        return [
            'id' => $this->faker->uuid(),
            'title' => "{$brand} {$noun} {$adjective}",
            'brand' => $brand,
            'category_id' => $categoryId,
            'price' => $this->faker->randomFloat(2, 5, 999999) * 1,
            'currency' => $this->faker->randomElement($currencies),
            'stock' => $this->faker->numberBetween(0, 500),
            'seller_id' => $sellerId,
            'rating' => $this->faker->randomFloat(1, 3.0, 5.0),
            'popularity' => $this->faker->numberBetween(0, 1000),
            'attributes' => array_filter($attrs, fn($v) => $v !== null),
        ];
    }
}
