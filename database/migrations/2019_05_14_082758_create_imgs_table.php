<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('imgs',function(Blueprint $table){
               $table->increments('id')->comment('主键id');
               $table->integer('user_id')->comment('上传者用户id');
               $table->unsignedInteger('pano_id')->comment('漫游场景id');
               $table->string('name')->comment('图片名');
               $table->string('thumb')->nullable()->comment('缩略图');
               $table->string('length')->comment('图片大小');
               $table->tinyInteger('watermark')->default(2)->comment('是否添加水印 1=>添加 2=>不添加');
               $table->tinyInteger('isfckedit')->default(2)->comment('是否通过富媒体编辑器fckedit上传 1=>是 2=>不是');
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
        Schema::dropIfExists('imgs');
    }
}
