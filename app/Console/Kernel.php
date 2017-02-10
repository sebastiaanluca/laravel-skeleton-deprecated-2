<?php

namespace App\Console;

use App\Console\Commands\ClearQueue;
use App\Console\Commands\DumpMySqlDatabases;
use App\Console\Commands\ImportMySqlDump;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ClearQueue::class,
        DumpMySqlDatabases::class,
        ImportMySqlDump::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
