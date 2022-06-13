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
        Schema::create('playlists', function (Blueprint $table) {
            $table->bigInteger('id')->index();
            $table->string('name');
            $table->bigInteger('songId')->unsigned()->index();
            $table->foreign('songId')->references('id')->on('songs')->cascade();
            $table->date('createdDate');
            $table->bigInteger('userId')->unsigned()->index();
            $table->foreign('userId')->references('id')->on('users');
            $table->timestamps = FALSE;
            $table->primary(['id', 'songId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playlists');
    }
};
