<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panos', function (Blueprint $table) {
            $table->increments('id');

            $table->integer("pano_id")->default(0)->comment('全景ID');

            $table->string('house_name')->comment('楼盘名称');
            $table->string('house_used')->comment('房源类型');
            $table->string('house_type')->comment('户型');
            $table->string('house_area')->comment('面积');
            $table->string('panoUrl')->nullable()->comment('场景路径');
            $table->longText("remark")->comment("备注");

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
        Schema::dropIfExists('panos');
    }
}
