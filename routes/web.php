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
Route::post('krpano/exec', 'Krpano\\UploadController@panosExec');                     //多图片 合成全景api
//Route::post('krpano/panos', 'Krpano\\UploadController@panosExec');                  //多图片 切片漫游api

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
    $fileName = $panoId.".txt";
    $filePath = storage_path("files\\").$fileName;

    if (file_exists($filePath)){
        $tourXml = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $tourXmlStr = file_get_contents($tourXml);
        $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
        $button_3 = $tourXmlObj->layer[16];
        $button_3["onclick"] = "if(layer[iframelayer].visible,
    set(layer[top_shade_layer].visible,false);
        set(layer[top_back_layer].visible,true);
        remove_iframe(iframelayer);
        set(layer[icon].url, '/pano/storage/static/images/down.png');
        set(layer[iframelayer].visible,false),
        set(layer[top_back_layer].visible,false);
        set(layer[top_shade_layer].visible,true);
        set(layer[top_shade_layer_pc].visible,true);
        set(layer[set_alert_table].visible,false);
        set(layer[iframelayer_new].visible,false);
        remove_iframe(iframelayer_new);
        set(layer[icon].url, '/pano/storage/static/images/up.png');
        call_iframe(iframelayer,/pano/krpano/fr/".$panoId."););";
        file_put_contents($tourXml, $tourXmlObj->asXML());

        $houseInfo = file_get_contents($filePath);
        $houseInfo = json_decode($houseInfo);

        //$houseInfo = Cache::get("houseInfo"."_".$panoId,"");
        return view('krpano.fr',['houseInfo'=>$houseInfo]);
    }else{
        $tourXml = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $tourXmlStr = file_get_contents($tourXml);
        $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
        $button_3 = $tourXmlObj->layer[16];
        $button_3["onclick"] = "";
        file_put_contents($tourXml, $tourXmlObj->asXML());
    }
});


//Route::get('/vr/uri/{gid}/hc={houseId}&ac={agentId}&cs={cityID}', 'Krpano\\UploadController@getPanoUri');
Route::get('/vr/uri/{gid}', 'Krpano\\UploadController@getPanoUri');

Route::get('/vr/check/{houseCode}/{check_at}', 'Krpano\\UploadController@checkRule');

Route::post('/vr/make', 'Krpano\\UploadController@makeHouseApi');


//分享
/*Route::get('/krpano/wechat/{panoId}', function($panoId){
    return view('krpano.share');
});*/

//经纪人信息
Route::get('/krpano/vr/{panoId}', function ($panoId){
    $agentInfo = Cache::get("agentInfo"."_".$panoId,"");
    return view('krpano.vr',['agentInfo'=>$agentInfo]);
});

//扫码拨号
Route::get('krpano/code/{panoId}', function ($panoId){
    $chatCode = Cache::get("chatCode"."_".$panoId,"");
    return view('krpano.code',['chatCode'=>$chatCode]);
});

//扫码分享
Route::get('krpano/share/{panoId}', function ($panoId){

    $sweepShare = DB::select('select panoUrl from panos  where gid=?', [$panoId]);
    $panoUrl = $sweepShare[0]->panoUrl;

    $uriQuery = parse_url($panoUrl, PHP_URL_QUERY);
    $arrQuery = convertUrlQuery($uriQuery);     //将地址参数字符串变为数组
    $arrQuery = json_encode($arrQuery);

    return view('krpano.sweepShare',['panoUrl'=>$panoUrl,"arrQuery"=>$arrQuery]);
});


//维护人头像
Route::get('krpano/agent/{panoId}', function ($panoId){
    $agentInfo = Cache::get("agentInfo"."_".$panoId,"");
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



