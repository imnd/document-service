<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewTableGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');

            $table->jsonb('title');
            $table->jsonb('placeholder')->nullable()->default(null);
            $table->jsonb('description')->nullable()->default(null);

            $table->string('type', 50)->default(null);
            $table->jsonb('options')->nullable()->default(null);

            $table->integer('user_id')->unsigned()->nullable()->default(null);

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
        Schema::dropIfExists('groups');
    }
}
