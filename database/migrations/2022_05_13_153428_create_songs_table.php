<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('lyricPath')->nullable();
            $table->bigInteger('singerId')->unsigned()->index();
            $table->foreign('singerId')->references('id')->on('singers')->cascade();
            $table->boolean('verified');
            $table->bigInteger('createdBy')->unsigned()->index();
            $table->foreign('createdBy')->references('id')->on('users');
            $table->timestamps = FALSE;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
};
