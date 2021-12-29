<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientGeoLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_geo_locations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('userId');

            $table->string('clientId');

            $table->string('latitude')->nullable();

            $table->string('longitude')->nullable();

            $table->string('kilometreRadius')->nullable();

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
        Schema::dropIfExists('client_geo_locations');
    }
}
