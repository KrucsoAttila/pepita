<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seller;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        Seller::factory(15)->create();
    }
}
