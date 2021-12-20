<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientServiceAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_service_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('userId')->nullable();

            $table->string('gatewayId')->nullable();
            $table->string('serviceId')->nullable();
            $table->string('clientId')->nullable();

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
        Schema::dropIfExists('client_service_accesses');
    }
}
