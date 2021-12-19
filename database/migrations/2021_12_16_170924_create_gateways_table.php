<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('userId');

            $table->string('gatewayTitle');
            $table->string('gatewayInfo')->nullable();

            $table->string('gatewayIP')->nullable();
            $table->longText('gatewayAccessToken')->nullable();
            $table->longText('gatewayServicesList')->nullable();

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
        Schema::dropIfExists('gateways');
    }
}
