<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnortAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snort_alerts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('userId')->nullable();
            $table->integer('gatewayId')->nullable();

            $table->integer('incidentResponseId')->nullable();
            $table->integer('incidentResponseStatus')->default(0);

            $table->longText('snortFullAlert')->nullable();

            $table->string('snortAlertCode')->nullable();
            $table->string('snortAlertTitle')->nullable();
            $table->string('snortAlertClassification')->nullable();
            $table->string('snortAlertPriority')->nullable();

            $table->string('srcIP')->nullable();
            $table->string('dstIp')->nullable();

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
        Schema::dropIfExists('snort_alerts');
    }
}
