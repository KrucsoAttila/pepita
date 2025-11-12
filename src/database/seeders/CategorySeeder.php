<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $fixed = [
            ['name'=>'Pelenkák','path'=>'Baba/Mama > Pelenkák'],
            ['name'=>'Okostelefonok','path'=>'Elektronika > Telefonok > Okostelefonok'],
            ['name'=>'Ultrabook','path'=>'Elektronika > Laptopok > Ultrabook'],
            ['name'=>'Hajápolás','path'=>'Szépség > Hajápolás'],
            ['name'=>'Edények','path'=>'Otthon > Konyha > Edények'],
            ['name'=>'Futócipők','path'=>'Sport > Futás > Cipők'],
            ['name'=>'Kabátok','path'=>'Ruházat > Női > Kabátok'],
            ['name'=>'Pólók','path'=>'Ruházat > Férfi > Pólók'],
            ['name'=>'Társasjátékok','path'=>'Játék > Társasjátékok'],
            ['name'=>'Kutya száraz','path'=>'Állateledel > Kutya > Száraz'],
            ['name'=>'Világítás','path'=>'Otthon > Világítás'],
            ['name'=>'Szerszámok','path'=>'Otthon > Barkács > Szerszámok'],
        ];
        foreach ($fixed as $c) {
            Category::factory()->create($c);
        }
        Category::factory(5)->create();
    }
}
