<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayNetworkTrafficTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_network_traffic', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('userId');

            $table->string('gatewayId')->nullable();

            $table->string('rxCount')->nullable();

            $table->string('txCount')->nullable();

            $table->string('cpuPercent')->nullable();

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
        Schema::dropIfExists('gateway_network_traffic');
    }
}
