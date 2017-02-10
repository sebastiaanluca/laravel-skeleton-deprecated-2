<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ImportMySqlDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a MySQL database dump from a .sql file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $process = new Process("mysql -u $username -p$password < $file");

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info("Imported all MySQL databases from $file!");
    }
}
