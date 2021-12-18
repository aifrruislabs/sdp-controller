<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrustScoreWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trust_score_weights', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('userId');

            $table->string('scoreWeight1')->nullable();
            $table->string('scoreWeight2')->nullable();
            $table->string('scoreWeight3')->nullable();
            $table->string('scoreWeight4')->nullable();
            $table->string('scoreWeight5')->nullable();

            $table->string('scoreWeight6')->nullable();
            $table->string('scoreWeight7')->nullable();
            $table->string('scoreWeight8')->nullable();
            $table->string('scoreWeight9')->nullable();
            $table->string('scoreWeight10')->nullable();

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
        Schema::dropIfExists('trust_score_weights');
    }
}
