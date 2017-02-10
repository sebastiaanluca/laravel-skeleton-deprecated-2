<?php

use Illuminate\Database\Schema\Blueprint;
use SebastiaanLuca\Migrations\Migration;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Execute the migration.
     */
    protected function migrateUp()
    {
        $this->schema->create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
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
