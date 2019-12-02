<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaracatchStorageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laracatch', function (Blueprint $table)
        {
            $table->char('id', 36)->primary();
            $table->longText('data');
            $table->string('seen_at');
            $table->text('location');
            $table->boolean('console');
            $table->string('ip');
            $table->string('method');

            $table->index('seen_at');
            $table->index('console');
            $table->index('ip');
            $table->index('method');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('laracatch');
    }
}
