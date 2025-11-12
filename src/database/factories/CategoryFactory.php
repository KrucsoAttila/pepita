<?php
namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $paths = [
            'Baba/Mama > Pelenkák',
            'Elektronika > Telefonok > Okostelefonok',
            'Elektronika > Laptopok > Ultrabook',
            'Szépség > Hajápolás',
            'Otthon > Konyha > Edények',
            'Sport > Futás > Cipők',
            'Ruházat > Női > Kabátok',
            'Ruházat > Férfi > Pólók',
            'Játék > Társasjátékok',
            'Állateledel > Kutya > Száraz'
        ];
        $path = $this->faker->randomElement($paths);
        $name = Str::afterLast($path, ' > ') ?: $path;

        return [
            'id' => $this->faker->uuid(),
            'name' => $name,
            'path' => $path,
        ];
    }
}
