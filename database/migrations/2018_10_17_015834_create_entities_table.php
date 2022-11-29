<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', config('entities.types'));
            
            $table->integer('main_id')->unsigned()->nullable();
            $table->foreign('main_id')->references('id')->on('entities')->onDelete('cascade');
            
            $table->timestamps();
        });

        Schema::create('entity_datas', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('entity_id')->unsigned();
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade')->onUpdate('cascade');
            
            $table->integer('version')->unsigned()->default(1);
            $table->integer('merged')->unsigned()->default(1);
            $table->integer('user_id')->unsigned()->nullable()->default(null);
    
            $table->jsonb('payload')->nullable();
            $table->jsonb('diff')->nullable();
    
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
        Schema::dropIfExists('entity_datas');
        Schema::dropIfExists('entities');
    }
}
