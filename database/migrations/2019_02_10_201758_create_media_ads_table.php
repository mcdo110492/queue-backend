<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source', 250);
            $table->string('media_type', 50)->default('video/mp4');
            $table->integer('weight')->default(1);
            $table->smallInteger('visibility')->default(1);
            $table->string('title', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_ads');
    }
}