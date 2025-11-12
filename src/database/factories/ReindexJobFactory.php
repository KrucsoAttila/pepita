<?php
// File: database/factories/ReindexJobFactory.php
namespace Database\Factories;

use App\Models\ReindexJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReindexJobFactory extends Factory
{
    protected $model = ReindexJob::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'status' => 'pending',
            'recreate' => false,
            'error' => null,
            'started_at' => null,
            'finished_at' => null,
        ];
    }
}
