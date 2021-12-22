<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrustScoreTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trust_score_trackers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('userId');

            $table->string('clientId')->nullable();
            $table->string('trustScoreFactorId')->nullable();
            $table->string('percentScored')->nullable();

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
        Schema::dropIfExists('trust_score_trackers');
    }
}
