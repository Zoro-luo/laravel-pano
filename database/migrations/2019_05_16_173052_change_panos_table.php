<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePanosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('panos', function (Blueprint $table) {
            $table->integer('user_id')->after('id')->default(0)->comment('上传者用户id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('panos',function(Blueprint $table){
            $table->dropColumn('user_id');
        });
    }
}
