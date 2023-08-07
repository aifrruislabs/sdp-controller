<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_responses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('userId')->nullable();
            $table->integer('gatewayId')->nullable();

            $table->integer('snortAlertId')->nullable();
            $table->integer('snortIncidentTypeId')->nullable();
            $table->integer('incidentResponseActionId')->nullable();

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
        Schema::dropIfExists('incident_responses');
    }
}
