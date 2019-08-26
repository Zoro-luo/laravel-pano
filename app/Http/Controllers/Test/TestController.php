<?php

namespace App\Http\Controllers\Test;

use App\Model\Pano;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function error()
    {
    }

    public function index(Request $request)
    {
        return "TEST";
    }

    public function cache1()
    {
        Cache::put('key3','val3',10);         //设置缓存
        //$bool = Cache::add('key4','val4',10); // 设置缓存 已经有同名key就返回false  没有就返回true
        //var_dump($bool);
        //Cache::forever('key3','val3');        //永久设置缓存

        /*if(Cache::has('key1')){
            $val = Cache::get('key1');
            var_dump($val);
        }else{
            var_dump('a');
        }*/
    }

    public function cache2()
    {
        //$getData= Cache::get('key2');       //get 只是取缓存数据
        //$pullData = Cache::pull('key3');    //取出一次 然后删除掉
        $bool = Cache::forget('key3');        //从缓存中删除对象 删除成功返回true 失败返回false
        var_dump($bool);
    }

}
