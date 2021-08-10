<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTxs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('txs', function (Blueprint $table) {
            $table->id();
            $table->string('email_user');
            $table->foreign('email_user')->references('email')->on('users')->onDelete('cascade');
            $table->string('tx');
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
        Schema::dropIfExists('txs');
    }
}
