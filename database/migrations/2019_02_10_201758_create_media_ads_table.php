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
            $table->string('media_path', 250)->default('default.jpg');
            $table->string('alt_text', 150)->default("This is an alt text");
            $table->integer('weight')->default(1);
            $table->smallInteger('visibility')->default(1);
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