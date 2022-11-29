<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserIdFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function ($table) {
            $table->index('type');
        });

        Schema::table('entity_datas', function ($table) {
            $table->string('user_id')->nullable()->default(null)->change();
            $table->index('user_id');
        });

        Schema::table('user_group', function ($table) {
            $table->string('user_id')->nullable()->default(null)->change();
            $table->index('user_id');
            $table->index('group_type');
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('fields', function ($table) {
            $table->index('user_id');
        });

        Schema::table('group_fields', function ($table) {
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('field_id')->references('id')->on('fields');
        });

        Schema::table('groups', function ($table) {
            $table->index('user_id');
            $table->index('type');
        });

        Schema::table('user_group_fields', function ($table) {
            $table->foreign('user_group_id')->references('id')->on('user_group');
            $table->foreign('field_id')->references('id')->on('fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
