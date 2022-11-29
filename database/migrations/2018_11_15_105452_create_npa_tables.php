<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNpaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('npas', function (Blueprint $table) {
            $table->increments('id');
            $table->jsonb('title')->nullable();
            
            $table->integer('main_id')->unsigned()->nullable();
            $table->foreign('main_id')->references('id')->on('npas')->onDelete('set null')->onUpdate('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('npa_links', function(Blueprint $table) {
            $table->increments('id');
            
            $table->integer('npa_id')->unsigned();
            $table->foreign('npa_id')->references('id')->on('npas')->onDelete('cascade')->onUpdate('cascade');
            
            $table->string('link', 1500)->nullable();
            $table->jsonb('payload')->nullable();
            
            $table->timestamps();
        });
        
        Schema::create('npa_linkables', function(Blueprint $table) {
            $table->increments('id');
            
            $table->integer('npa_link_id')->unsigned();
            $table->foreign('npa_link_id')->references('id')->on('npa_links')->onDelete('cascade')->onUpdate('cascade');
            
            $table->integer('npa_linkable_id')->index();
            $table->string( 'npa_linkable_type',500)->index();
            
            $table->boolean('is_public')->default(1)->index();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('npa_linkables');
        Schema::dropIfExists('npa_links');
        Schema::dropIfExists('npas');
    }
}
