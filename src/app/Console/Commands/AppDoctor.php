<?php
// File: app/Console/Commands/AppDoctor.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Application as ArtisanApp;
use Illuminate\Contracts\Foundation\Application as LaravelApp;

class AppDoctor extends Command
{
    protected $signature = 'app:doctor';
    protected $description = 'Diagnose Artisan command registration';

    public function handle(LaravelApp $app): int
    {
        /** @var ArtisanApp $artisan */
        $artisan = $app->make(ArtisanApp::class, ['version' => 'CLI']);
        $commands = array_keys($artisan->all());

        $this->info('Detected commands: '.count($commands));
        $this->line(implode(', ', $commands));

        $path = base_path('app/Console/Commands');
        $this->line('Commands path: '.$path.' ('.(is_dir($path) ? 'exists' : 'MISSING').')');

        $expects = ['inspire','test','tinker','products:es-init'];
        foreach ($expects as $name) {
            $this->line(sprintf('[%s] %s', in_array($name, $commands, true) ? 'OK' : 'NO', $name));
        }

        return self::SUCCESS;
    }
}
