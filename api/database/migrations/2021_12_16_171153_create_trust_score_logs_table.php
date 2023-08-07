<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrustScoreLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trust_score_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('userId');

            $table->string('clientId')->nullable();
            $table->string('trustScoreFactorId')->nullable();
            $table->string('actionStatus')->nullable();

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
        Schema::dropIfExists('trust_score_logs');
    }
}
