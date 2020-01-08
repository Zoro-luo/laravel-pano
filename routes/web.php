<?php

/*
|--------------------------------------------------------------------------
| web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Cache;

Auth::routes();

include __DIR__.'/admin/web.php';

//首页
Route::get('/', 'HomeController@index');

//前台组
Route::group(['middleware' => ['auth.member']], function () {
    //Route::get('/', 'HomeController@index');
    //测试TEST
    Route::get('member/test',function(){
        dd(auth()->guard('member')->user()->name);
    });


});


//////// 多图片表单上传
Route::get('krpano/indexs', 'Krpano\\UploadController@indexs');                     //多图片 上传全景图表单
Route::post('krpano/uploads', 'Krpano\\UploadController@panos');                     //多图片 上传全景api
Route::post('krpano/panos', 'Krpano\\UploadController@panosExec');                  //多图片 切片漫游api

/**
 * 前台登录注册
 */
Route::any('member/register','AuthMember\\LoginController@register');
Route::any('member/login','AuthMember\\LoginController@login');
Route::post('member/logout','AuthMember\\LoginController@logout');


//生成全景漫游路由组
Route::get('/pano/{id}', function ($id) {                               //用于iframe来展示的漫游
    return redirect('/storage/pano/' . $id . '/tour.html');
});
Route::get('/show/{id}', 'Krpano\\PanoController@show');                //显示漫游页

///////// 单图片表单上传全景
Route::get('krpano/index', 'Krpano\\PanoController@index');             //上传全景图表单
Route::post('krpano/upload', 'Krpano\\PanoController@uploadImgs');      //上传全景api
Route::post('krpano/pano', 'Krpano\\PanoController@panoImgs');          //切片漫游api

//房源信息
Route::get('/krpano/fr/{panoId}', function($panoId){

    $houseInfo = Cache::get("houseInfo"."_".$panoId,"NULL");
    return view('krpano.fr',['houseInfo'=>$houseInfo]);
});


//设置弹窗
/*Route::get('/krpano/set/{panoId}', function($panoId){
    //$houseInfo = Cache::get("houseInfo"."_".$panoId,"NULL");
    return view('krpano.set');
});*/


//经纪人信息
Route::get('/krpano/vr/{panoId}', function ($panoId){
    $agentInfo = Cache::get("agentInfo"."_".$panoId,"NULL");
    return view('krpano.vr',['agentInfo'=>$agentInfo]);
});

//扫码拨号
Route::get('krpano/code/{panoId}', function ($panoId){
    $agentInfo = Cache::get("agentInfo"."_".$panoId,"NULL");
    return view('krpano.code',['agentInfo'=>$agentInfo]);
});

//扫码分享
Route::get('krpano/share/{panoId}', function ($panoId){
    $agentInfo = Cache::get("agentInfo"."_".$panoId,"NULL");
    return view('krpano.sweepShare',['agentInfo'=>$agentInfo]);
});

//维护人头像
Route::get('krpano/agent/{panoId}', function ($panoId){
    $agentInfo = Cache::get("agentInfo"."_".$panoId,"NULL");
    return view('krpano.agent',['agentInfo'=>$agentInfo]);
});

//前台登录验证ajax
//Route::post('member/checkEmail', 'AuthMember\\LoginController@checkEmail');
















// ==== 前后端登录
//前端
/*Route::group(['middleware'=>['auth']],function(){
    Route::get('/', 'web\LoginController1@index')->name('front_index');
    Route::get('login', 'web\LoginController1@login');
    Route::post('login', 'web\LoginController1@store')->name('front_login');
    Route::get('logout', 'web\LoginController1@logout')->name('front_logout');
    Route::get('login-success', 'web\LoginController1@success')->name('front_success');
});*/
//后端
/*Route::group(['prefix' => 'admin', 'middleware' => ['adminVerify']], function () {
    Route::get('/', 'Admin\LoginController1@index')->name('admin_index');
    Route::get('login', 'Admin\LoginController1@login');
    Route::post('login', 'Admin\LoginController1@store')->name('admin_login');
    Route::get('logout', 'Admin\LoginController1@logout')->name('admin_logout');
    Route::get('login-success', 'Admin\LoginController1@success')->name('admin_success');
});*/
// ==== 前后端登录 END



