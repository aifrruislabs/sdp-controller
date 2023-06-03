<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrustScorePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trust_score_policies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('userId');

            $table->string('serviceId')->nullable();
            $table->string('scoreFactorId')->nullable();
            $table->string('scorePercent')->nullable();

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
        Schema::dropIfExists('trust_score_policies');
    }
}
