<?php

use Illuminate\Database\Schema\Blueprint;
use SebastiaanLuca\Migrations\Migration;

class CreateFailedJobsTable extends Migration
{
    /**
     * Execute the migration.
     */
    protected function migrateUp()
    {
        $this->schema->create('_failed_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migration.
     */
    protected function migrateDown()
    {
        $this->drop('_failed_jobs');
    }
}
