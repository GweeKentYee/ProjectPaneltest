<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameDataTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_data_types', function (Blueprint $table) {
            $table->id();
            $table->string('data_name')->required();
            $table->string('layer')->required();
            $table->string('player_related')->required();
            $table->unsignedBigInteger('games_id')->required();
            $table->timestamps();

            $table->foreign('games_id')->references('id')->on('games')->onDelete('cascade');
            $table->index('games_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_data_types');
    }
}
