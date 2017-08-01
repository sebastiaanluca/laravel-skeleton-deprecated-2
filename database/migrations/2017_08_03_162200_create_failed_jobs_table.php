<?php

use Illuminate\Database\Schema\Blueprint;
use SebastiaanLuca\Migrations\TransactionalMigration;

class CreateFailedJobsTable extends TransactionalMigration
{
    /**
     * Execute the migration.
     */
    protected function migrateUp()
    {
        $this->schema->create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');

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
        $this->drop('failed_jobs');
    }
}
