<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            
            $table->increments('id');

            $table->string('name')->comment('分类名');
            $table->integer('parent_id')->comment('父级ID');
            $table->string('path')->comment('路径');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->Integer('sort')->default(100)->comment('排序');

            $table->index('parent_id');
            $table->index('path');

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
        Schema::dropIfExists('categories');
    }
}
