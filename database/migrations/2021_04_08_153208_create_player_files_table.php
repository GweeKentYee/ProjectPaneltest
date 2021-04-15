<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_files', function (Blueprint $table) {
            $table->id();
            $table->string('JSON_file')->required();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('players_id')->required();
            $table->timestamps();

            $table->foreign('players_id')->references('id')->on('players')->onDelete('cascade');
            $table->index('players_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_files');
    }
}
