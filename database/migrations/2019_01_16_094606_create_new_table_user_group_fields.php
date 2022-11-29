<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewTableUserGroupFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_group_id')->unsigned()->nullable()->default(null);
            $table->integer('field_id')->unsigned()->nullable()->default(null);
            $table->string('value', 500)->default(null);
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
        Schema::dropIfExists('user_group_fields');
    }
}
