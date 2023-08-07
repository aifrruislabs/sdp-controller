<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayServiceStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_service_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('userId')->nullable();
            $table->string('gatewayId')->nullable();
            $table->string('serviceId')->nullable();
            $table->string('serviceStatus')->nullable();

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
        Schema::dropIfExists('gateway_service_statuses');
    }
}
