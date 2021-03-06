<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_number');
            $table->integer('department_id')->unsigned();
            $table->smallInteger('priority')->default(0);
            $table->smallInteger('status')->default(0);
            $table->string('priority_type')->nullable();
            $table->date('date_issued');
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
        Schema::dropIfExists('tickets');
    }
}