<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->smallInteger('status');
            $table->string('served_time',20)->nullable();
            $table->dateTime('complete_time');
            $table->timestamps();
        });

        /**
         * Ticket Status
         * 0 - Pending / Back To Queue
         * 1 - Called
         * 2 - Serving
         * 3 - Completed /Finished
         * 4 - Stopped
         */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets_users');
    }
}