<?php

use Illuminate\Database\Schema\Blueprint;
use SebastiaanLuca\Migrations\TransactionalMigration;

class CreatePasswordResetsTable extends TransactionalMigration
{
    /**
     * Execute the migration.
     */
    protected function migrateUp()
    {
        $this->schema->create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();

            $table->string('token');

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migration.
     */
    protected function migrateDown()
    {
        $this->drop('password_resets');
    }
}
