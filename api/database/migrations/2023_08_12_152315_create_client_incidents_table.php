<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_incidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('clientId')->nullable();
            $table->integer('snortAlertId')->nullable();
            $table->integer('snortClassId')->nullable();
            $table->integer('incidentResponseId')->nullable();
            $table->string('incidentTitle')->nullable();
            $table->string('incidentResponse')->nullable();
            $table->string('incidentResolved')->default(0);
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
        Schema::dropIfExists('client_incidents');
    }
}
