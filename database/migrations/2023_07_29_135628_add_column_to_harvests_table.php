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
        Schema::table('harvests', function (Blueprint $table) {
            $table->integer('retail_price')->after('price');
            $table->integer('retail_minimum')->after('retail_price');
            $table->tinyInteger('harvest_month')->after('retail_minimum');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('harvests', function (Blueprint $table) {
            $table->dropColumn('retail_price');
            $table->dropColumn('retail_minimum');
            $table->dropColumn('harvest_month');
            $table->tinyInteger('status');
        });
    }
};
