<?php

namespace App\Console\Commands;

use DB;
use Exception;
use Illuminate\Console\Command;

class ClearQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all queued database jobs.';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $queue = config('queue.default');

        if ($queue !== 'database') {
            throw new Exception('Unsupported queue driver.');
        }

        DB::table(config('queue.connections.database.table'))->truncate();
        DB::table(config('queue.failed.table'))->truncate();

        $this->info('Cleared queue and failed jobs!');
    }
}
