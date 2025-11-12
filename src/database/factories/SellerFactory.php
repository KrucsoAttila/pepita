<?php
namespace Database\Factories;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->company(),
            'rating' => $this->faker->randomFloat(1, 3.5, 5.0),
        ];
    }
}
