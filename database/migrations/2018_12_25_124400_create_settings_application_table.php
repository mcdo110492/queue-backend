<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings_application', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name');
            $table->json('address');
            $table->json('phone_number');
            $table->string('company_logo')->default('company-logo.png');
            $table->string('company_favicon')->default('favicon.ico');
            $table->string('company_color')->default('blue');
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
        Schema::dropIfExists('settings_application');
    }
}
