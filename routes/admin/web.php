<?php

//后台组
Route::group(['middleware' => ['auth']], function () {

    //后台首页
    Route::get('/admin', 'Admin\\IndexController@index');
    Route::get('user/index', 'Admin\\UserController@index');
    Route::get('pano/index', 'Admin\\PanoController@index');

    //Route::get('admin/indexspot/{pano_id}', 'Admin\\PanoController@indexHotspot');
    Route::get('admin/indexhs/{pano_id}', 'Admin\\PanoController@indexNewSpot');

    Route::post('admin/keepspot', 'Admin\\PanoController@keepHotspot');
    Route::post('admin/changespot', 'Admin\\PanoController@changeHotspot');

    Route::post('admin/addspot', 'Admin\\PanoController@addHotspot');       //添加热点
    Route::post('admin/editspot', 'Admin\\PanoController@editHotspot');     //编辑热点拖拽
    Route::post('admin/ediths', 'Admin\\PanoController@editHs');            //编辑热点坐标
    Route::post('admin/delspot', 'Admin\\PanoController@deleteHotspot');    //删除热点

    Route::post('admin/startup', 'Admin\\PanoController@startup');              //设定启动视角
    Route::post('admin/setcover', 'Admin\\PanoController@setcover');            //设定启动全景
    Route::post('admin/toggleHs', 'Admin\\PanoController@toggleHs');            //显示/隐藏热点

});

/**
 * VR 1.0
 */
Route::get('vr/list', 'Admin\\VrController@index');                         //vr列表
Route::get('vr/edit/{pano_id}', 'Admin\\VrController@update');              //热点编辑页
Route::get('vr/view/{pano_id}', 'Admin\\VrController@preview');             //预览模型

Route::post('vr/preview', 'Admin\\VrController@copyUrl');             //预览模型

Route::post('vr/showlabel', 'Admin\\VrController@showLabel');               //ajax 热点管理场景下拉
Route::post('vr/setcover', 'Admin\\VrController@setcover');                 //设置为封面
Route::post('vr/toggle', 'Admin\\VrController@toggleHot');                  //显示/隐藏热点
Route::post('vr/savespot', 'Admin\\VrController@saveHotspot');              //ajax 添加热点坐标
Route::post('vr/esspot', 'Admin\\VrController@editSaveHotspot');            //ajax  编辑后的添加热点坐标
Route::post('vr/savepoint', 'Admin\\VrController@savePoint');               //ajax  添加文本标签
Route::post('vr/espoint', 'Admin\\VrController@editSavePoint');             //ajax  编辑后的添加文本标签热点
Route::post('vr/editspot', 'Admin\\VrController@editHotspot');              //ajax  编辑热点坐标
Route::post('vr/checkerr', 'Admin\\VrController@checkRedErr');              //ajax  点编辑弹出热点后点红X的操作
Route::post('vr/delspot', 'Admin\\VrController@delHotspot');                //ajax  点击删除的弹窗操作
Route::post('vr/delhs', 'Admin\\VrController@delHs');                       //ajax  删除弹窗的确认操作


/**
 * VR 2.0
 */
Route::get('kp/list', 'Admin\\KpController@index');                         //vr列表
Route::get('kp/edit/{pano_id}', 'Admin\\KpController@update');              //热点编辑页
Route::get('kp/view/{pano_id}', 'Admin\\KpController@preview');             //预览模型

Route::post('kp/showlabel', 'Admin\\KpController@showLabel');               //ajax 热点管理场景下拉
Route::post('kp/setcover', 'Admin\\KpController@setcover');                 //设置为封面
Route::post('kp/toggle', 'Admin\\KpController@toggleHot');                  //显示/隐藏热点
Route::post('kp/savespot', 'Admin\\KpController@saveHotspot');              //ajax 添加热点坐标
Route::post('kp/esspot', 'Admin\\KpController@editSaveHotspot');            //ajax  编辑后的添加热点坐标
Route::post('kp/savepoint', 'Admin\\KpController@savePoint');               //ajax  添加文本标签
Route::post('kp/espoint', 'Admin\\KpController@editSavePoint');             //ajax  编辑后的添加文本标签热点
Route::post('kp/editspot', 'Admin\\KpController@editHotspot');              //ajax  编辑热点坐标
Route::post('kp/checkerr', 'Admin\\KpController@checkRedErr');              //ajax  点编辑弹出热点后点红X的操作
Route::post('kp/delspot', 'Admin\\KpController@delHotspot');                //ajax  点击删除的弹窗操作
Route::post('kp/delhs', 'Admin\\KpController@delHs');                       //ajax  删除弹窗的确认操作
