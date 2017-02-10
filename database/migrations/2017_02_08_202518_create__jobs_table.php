<?php

use Illuminate\Database\Schema\Blueprint;
use SebastiaanLuca\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Execute the migration.
     */
    protected function migrateUp()
    {
        $this->schema->create('_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue');
            $table->longText('payload');
            $table->tinyInteger('attempts')->unsigned();
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');

            $table->index(['queue', 'reserved_at']);
        });
    }

    /**
     * Reverse the migration.
     */
    protected function migrateDown()
    {
        $this->drop('_jobs');
    }
}
