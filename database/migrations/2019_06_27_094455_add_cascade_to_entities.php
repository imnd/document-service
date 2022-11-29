<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeToEntities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entity_datas', function($table) {
            $table->dropForeign(['entity_id']);

            $table->foreign('entity_id')
                ->references('id')
                ->on('entities')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entity_datas', function($table) {
            $table->dropForeign(['entity_id']);

            $table->foreign('entity_id')
                ->references('id')
                ->on('entities');
        });
    }
}
