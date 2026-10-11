<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Pengaman: RefreshDatabase mengosongkan database. Test hanya boleh berjalan di SQLite
        // in-memory, walaupun dijalankan di container/terminal yang env-nya menunjuk database asli.
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$connection}.database");

        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new \RuntimeException("Tests refused to run on [{$connection}:{$database}]; they must use SQLite :memory: (see phpunit.xml).");
        }

        return $app;
    }
}
