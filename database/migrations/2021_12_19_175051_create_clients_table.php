<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
     
            $table->string('userId');

            $table->string('clientId')->nullable();

            $table->string('firstName')->nullable();
            $table->string('middleName')->nullable();
            $table->string('lastName')->nullable();

            $table->string('username')->nullable();
            $table->longText('password')->nullable();

            $table->string('totalTrustScore')->nullable();
            $table->string('totalAccessScore')->nullable();
            $table->longText('encryptionKey')->nullable();
            $table->longText('hmacKey')->nullable();

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
        Schema::dropIfExists('clients');
    }
}
