<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotspotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotspots', function (Blueprint $table) {
            $table->increments('id');

            $table->integer("pano_id")->comment('全景ID');
            $table->string("sceneName")->comment("场景名");
            $table->string("hotsName")->comment("热点名");
            $table->string("ath")->comment("热点ath");
            $table->string("atv")->comment("热点atv");

            $table->string("linkedscene")->default("")->comment("热点跳转场景");
            $table->string("visible")->default("true")->comment("软删除");

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
        Schema::dropIfExists('hotspots');
    }
}
