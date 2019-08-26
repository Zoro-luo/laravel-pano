<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('imgs',function(Blueprint $table){
            $table->string('mb_name')->after('name')->nullable()->comment('中文名');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imgs',function(Blueprint $table){
            $table->dropColumn('mb_name');
        });
    }
}
