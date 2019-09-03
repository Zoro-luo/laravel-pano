<?php

//后台组
Route::group(['middleware' => ['auth']], function () {

    //后台首页
    Route::get('/admin', 'Admin\\IndexController@index');
    Route::get('user/index', 'Admin\\UserController@index');
    Route::get('pano/index', 'Admin\\PanoController@index');

    Route::get('admin/indexspot/{pano_id}', 'Admin\\PanoController@indexHotspot');
    Route::post('admin/keepspot', 'Admin\\PanoController@keepHotspot');
    Route::post('admin/changespot', 'Admin\\PanoController@changeHotspot');

    Route::post('admin/addspot', 'Admin\\PanoController@addHotspot');       //添加热点
    Route::post('admin/editspot', 'Admin\\PanoController@editHotspot');     //编辑热点拖拽
    Route::post('admin/ediths', 'Admin\\PanoController@editHs');            //编辑热点坐标
    Route::post('admin/delspot', 'Admin\\PanoController@deleteHotspot');    //删除热点

    Route::post('admin/startup', 'Admin\\PanoController@startup');          //设定启动视角
});
