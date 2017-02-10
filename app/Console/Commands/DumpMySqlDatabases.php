<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DumpMySqlDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump all MySQL databases to a .sql file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->format('Ymd-His');
        $file = "storage/app/mysql/mysqldump-$date.sql";

        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $process = new Process("mysqldump -u $username -p$password --all-databases > $file");

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info("Exported all MySQL databases to $file!");
    }
}
