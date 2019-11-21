<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>vr-详情页</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('public/static/hotsport')}}/css/p.min.css">
    <script src="{{asset('public/static/pano/js')}}/tour.js"></script>
    <script src="https://cdn.bootcss.com/vue/2.6.10/vue.min.js"></script>
    <style>
        .p-fixed {
            position: fixed;
            z-index: 99;
        }
    </style>
</head>
<body>
{{--<div class="header"></div>--}}
<div class="container containers">
    <div class="vr-container">
        <h1 class="vr-title">编辑模型</h1>
        <div class="vr-wrapper">
            <div class="vr-left">
                <div id="pano" class="vr-maps">
                    <noscript>
                        <table style="width:100%;height:100%;">
                            <tr style="vertical-align:middle;">
                                <td>
                                    <div style="text-align:center;">ERROR:<br/><br/>Javascript not activated<br/><br/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </noscript>
                    <script>
                        var krpano = null;
                        var sign = null;
                        var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
                        embedpano({
                            swf: "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.swf",
                            id: "krpanoSWFObject",
                            xml: xmlPath,
                            target: "pano",
                            html5: "auto",
                            passQueryParameters: true,
                            onready: krpano_onready_callback,
                        });

                        function krpano_onready_callback(krpano_interface) {
                            krpano = krpano_interface;
                        }
                    </script>
                </div>
            </div>
            <div class="vr-right">
                <div class="set-cover-box section">
                    <div class="my-btn my-btn-green set-covers" onclick="setScene()">设置为封面图</div>
                    <span>已设置：<span class="isScene" onclick="test()">{{$sceneTitle}}</span></span>
                </div>
                <div class="set-hot-choice section">
                    <h3 class="s-title">热点选择</h3>
                    <div class="hot-label">
                        <p>场景跳转标签</p>
                        <ul>
                            <li class=""><img onclick="addHotspots('hotspot');" class="gopng_red-point_outer makeHs"
                                              src="{{asset('public/static/hotsport')}}/css/icon/red-point.png"
                                              alt=""></li>
                        </ul>
                    </div>
                    <div class="hot-label">
                        <p>文字标签</p>
                        <ul>
                            <li class="gopng_font-label_outer"><img onclick="addHotspots('point');"
                                                                    src="{{asset('public/static/hotsport')}}/css/icon/font-label.png"
                                                                    alt=""></li>
                        </ul>
                    </div>
                </div>
                <div class="hot-show section">
                    <h3 class="s-title">热点展示</h3>
                    <div class="swich-box">
                        <label for=""  onclick="showHotsport()"><input type="checkbox" name="" id=""
                                                                         class="a-switch "></label>
                        <span class="txt-box">隐藏</span>
                    </div>
                </div>
                <div class="hot-manage section">
                    <h3 class="s-title">热点管理（<span class="redpoint-count">{{$count}}</span>条）</h3>
                    <div class="scroll-box">
                        <div class="my-table my-table-4 table-border table-small table-no-right-border">
                            <div class="table-title table-item">
                                <div class="table-text table-text1">
                                    <div class="my-select my-select1" @click-list="addLabel" tabindex="1">
                                        <div class="my-select-btn"><span class="btn-text">全部场景</span><i
                                                    class="iconfont iconUtubiao-13"></i>
                                        </div>
                                        <ul class="my-select-list">
                                            <li class="on">全部场景</li>
                                            <li>客厅</li>
                                            <li>餐厅</li>
                                            <li>主卧</li>
                                            <li>次卧</li>
                                            <li>阳台</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="table-text table-text2">热点类型</div>
                                <div class="table-text table-text3">标题</div>
                                <div class="table-text table-text4">操作</div>
                            </div>
                            <div class="table-content">
                                @foreach($panoData as $pano)
                                    <div class="table-item">
                                        <div class="table-text table-text1"><p>{{$pano->sceneName}}</p></div>
                                        @if($pano->type=="hotspot")
                                            <div class="table-text table-text2"><p>场景跳转</p></div>
                                        @else
                                            <div class="table-text table-text2"><p>文本标签</p></div>
                                        @endif
                                        <div class="table-text table-text3"><p>{{$pano->linkedscene}}</p></div>
                                        <div class="table-text table-text4">
                                            <i onclick="editHotspots('{{ $pano->hotsName}}');"
                                               class="iconfont iconbianji1"></i>
                                            <i onclick="delHotspots('{{ $pano->hotsName}}');"
                                               class="iconfont iconshanchu2"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-btn my-btn-gray preview ">预览</div>
                <div class="my-btn my-btn-green issue-btn" id="issueBtn">确认发布</div>
            </div>
        </div>
    </div>
</div>
</body>
<!-- 场景跳转弹窗 -->
<template id="sceneSetting">
    <div class="scene-setting">
        <div class="flex-list flex-column">
            <div class="flex-left">跳转场景</div>
            <div class="separator"></div>
            <div class="flex-right">
                <div class="article">
                    <div class="img-box">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-list">
            <div class="flex-left item-center"><span class="r-hint">*</span>标签名称</div>
            <div class="separator item-center">:</div>
            <div class="flex-right">
                <div class="my-input">
                    <input type="text" class="inputs monitor" placeholder="请输入5个中文字以内" maxlength="5">
                </div>
            </div>
        </div>
        <div class="btn-group">
            <div class="my-btn my-btn-cancel">取消</div>
            <div class="my-btn my-btn-green">确定添加</div>
        </div>
    </div>
</template>
<!-- 标题标签设置 -->
<template id="titleSetting">
    <div class="title-setting">
        <div class="flex-list">
            <div class="flex-left item-center"><span class="r-hint">*</span>标签名称</div>
            <div class="separator item-center">:</div>
            <div class="flex-right">
                <div class="my-input"><input type="text" class="inputs monitor input-point" placeholder="请输入10个中文字以内"
                                             maxlength="10"></div>
            </div>
        </div>
        <div class="btn-group">
            <div class="my-btn my-btn-cancel">取消</div>
            <div class="my-btn my-btn-green">确定添加</div>
        </div>
    </div>
</template>
<!-- 温馨提示 -->
<template id="cozyHint">
    <div class="cozy-hint">
        <div class="text"></div>
        <div class="btn-group">
            <div class="my-btn my-btn-cancel">取消</div>
            <div class="my-btn my-btn-green">确定</div>
        </div>
    </div>
</template>
<script src="{{asset('public/static/hotsport')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{asset('public/static/hotsport')}}/js/layer/layer.js"></script>
<script src="{{asset('public/static/hotsport')}}/js/common.js"></script>
<script src="{{asset('public/static/hotsport')}}/js/zxc_common.js"></script>
<script>

    function test() {

    }

    //添加热点
    function addHotspots(args) {
        if (krpano) {
            var h = krpano.get("view.hlookat").toFixed(3);
            var v = krpano.get("view.vlookat").toFixed(3);
            var sceneName = krpano.get("xml.scene");
            var sceneChname = krpano.get("scene[get(xml.scene)].title");
            var sceneIndex = krpano.get("scene[get(xml.scene)].index");
            var panoId = "{{$panoId}}";

            var hs_name = sceneName + '_' + Math.abs((Date.now() + Math.random()) | 0);
            krpano.call("addhotspot(" + hs_name + ")");
            if (args == "hotspot") {
                krpano.set("hotspot[" + hs_name + "].url", "{{asset('public/static/hotsport')}}/css/img/hotspot_max.png");
            } else if (args == "point") {
                krpano.set("hotspot[" + hs_name + "].url", "{{asset('public/static/hotsport')}}/css/img/hottext_max.png");
            } else {
                krpano.set("hotspot[" + hs_name + "].url", "{{asset('storage/panos/').'/'.$panoId}}/vtour/skin/vtourskin_hotspot.png");
            }
            krpano.set("hotspot[" + hs_name + "].ath", h);
            krpano.set("hotspot[" + hs_name + "].atv", v);
            krpano.set("hotspot[" + hs_name + "].zoom", "true");
            krpano.set("hotspot[" + hs_name + "].scale", "0.8");
            //右上角小红X 删除热点
            krpano.set("hotspot[" + hs_name + "].onloaded", "add_all_the_time_tooltip_error(" + hs_name + ");" +
                "set(plugin[get(linename)].onclick ,removehotspot(" + hs_name + ");set(plugin[get(linename)].visible,false););");
            krpano.set("hotspot[" + hs_name + "].visible", "true");
            krpano.set("hotspot[" + hs_name + "].ondown", "draghotspot();");

            if (krpano.get("device.html5") && args == "hotspot") {  //跳转热点
                krpano.set("hotspot[" + hs_name + "].onclick", function (hs) {

                    var offsetSize = 30;
                    var mx = krpano.get("mouse.x");
                    var my = krpano.get("mouse.y");
                    var mx_new = mx;
                    var my_new = my - offsetSize;
                    var pnt = krpano.screentosphere(mx, my);
                    var hh = pnt.x.toFixed(3);
                    var vv = pnt.y.toFixed(3);

                    var count = krpano.get("scene.count");
                    var templateStr = '';
                    for (var i = 0; i < count; i++) {
                        var imgUrl = krpano.get("scene[" + i + "].thumburl");
                        var sceneTitle = krpano.get("scene[" + i + "].title");
                        var template = `<div class="section">
                            <img src="${imgUrl}" alt="" data="${i}" class="img-child">
                            <h4 class="title">${sceneTitle}</h4>
                            </div>`;
                        templateStr += template;
                    }

                    //弹窗
                    myFun.layer.opens("#sceneSetting", "场景跳转设置", "normal", function (layero, index) {
                        var _this = this;
                        layero.find(".img-box").append(templateStr);
                        $(".img-child").click(function (e) {
                            var event = e || window.event
                            target = event.target || event.srcElement;
                            $(target).parents(".img-box").find(".img-border").removeClass("img-border");
                            $(target).addClass("img-border");
                            layero.find(".my-input>.inputs").val("");
                            layero.find(".my-input>.inputs").val($(target).parent(".section").find(".title").text());
                        })
                        layero.find(".my-btn-green").click(function () {  //点击确认
                            var flag = zFun.utils.validationAll();
                            var scene_index = $(target).attr("data");
                            var linkedscene = krpano.get("scene[" + scene_index + "].name");
                            var linkedTitle = krpano.get("scene[" + scene_index + "].title");
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                type: "POST",
                                url: "{{url('vr/savespot')}}",
                                data: {
                                    'hostName': hs_name,
                                    'h': hh,
                                    'v': vv,
                                    'sceneName': sceneChname,
                                    'sceneEname': sceneName,
                                    'panoId': panoId,
                                    'sceneIndex': sceneIndex,
                                    'linkedscene': linkedscene,
                                    'linkedTitle': linkedTitle
                                },
                                success: function (e) {
                                    if (flag) _this.close(index);
                                    var hotspotStr = '';
                                    for (var j = 0; j < e.count; j++) {
                                        var hsTemplate = `<div class="table-item">
                                            <div class="table-text table-text1"><p>${e.panoData[j]['sceneName']}</p></div>
                                            <div class="table-text table-text2"><p>${e.panoData[j]['type'] == "hotspot" ? "场景跳转" : "文本标签"}</p></div>
                                            <div class="table-text table-text3"><p>${e.panoData[j]['linkedscene']}</p></div>
                                             <div class="table-text table-text4">
                                                <i onclick="editHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconbianji1"></i>
                                                <i onclick="delHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconshanchu2"></i>
                                            </div>
                                        </div>`
                                        hotspotStr += hsTemplate;
                                    }
                                    $(".redpoint-count").text("").text(e.count);
                                    $(".hot-manage .my-table").find(".table-content").html("").append(hotspotStr);

                                    krpano.set("plugin[get(linename)].visible", "false");
                                    krpano.call("loadpano(tour.xml, NULL, MERGE, BLEND(0.1));");
                                    krpano.call("loadscene(" + e.sceneEname + ", NULL, MERGE, BLEND(0.1));");
                                }
                            });
                        })
                    });
                }.bind(null, hs_name));
            } else if (krpano.get("device.html5") && args == "point") {    //标签热点
                krpano.set("hotspot[" + hs_name + "].onclick", function (hs) {
                    var mx_p = krpano.get("mouse.x");
                    var my_p = krpano.get("mouse.y");
                    var pnt_p = krpano.screentosphere(mx_p, my_p);
                    var hh_p = pnt_p.x.toFixed(3);
                    var vv_p = pnt_p.y.toFixed(3);
                    //标题标签设置弹窗
                    myFun.layer.opens("#titleSetting", "标题标签设置", "small", function (layero) {
                        var _this = this;
                        layero.find(".my-btn-green").click(function () {
                            var flag = zFun.utils.validationAll();
                            if (flag) _this.close(index);
                            var tagTitle = $(".input-point").val();
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                type: "POST",
                                url: "{{url('vr/savepoint')}}",
                                data: {
                                    'hostName': hs_name,
                                    'h': hh_p,
                                    'v': vv_p,
                                    'sceneName': sceneChname,
                                    'sceneEname': sceneName,
                                    'panoId': panoId,
                                    'sceneIndex': sceneIndex,
                                    'linkedscene': tagTitle,
                                },
                                success: function (e) {
                                    if (flag) _this.close(index);
                                    var hotspotStr = '';
                                    for (var j = 0; j < e.count; j++) {
                                        var hsTemplate = `<div class="table-item">
                                            <div class="table-text table-text1"><p>${e.panoData[j]['sceneName']}</p></div>
                                            <div class="table-text table-text2"><p>${e.panoData[j]['type'] == "point" ? "文本标签" : "场景跳转"}</p></div>
                                            <div class="table-text table-text3"><p>${e.panoData[j]['linkedscene']}</p></div>
                                             <div class="table-text table-text4">
                                                <i onclick="editHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconbianji1"></i>
                                                <i onclick="delHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconshanchu2"></i>
                                            </div>
                                        </div>`
                                        hotspotStr += hsTemplate;
                                    }
                                    $(".redpoint-count").text("").text(e.count);
                                    $(".hot-manage .my-table").find(".table-content").html("").append(hotspotStr);
                                    krpano.call("loadpano(tour.xml, NULL, MERGE, BLEND(0.1));");
                                    krpano.call("loadscene(" + e.sceneEname + ", NULL, MERGE, BLEND(0.1));");
                                }
                            })
                        })
                    });
                }.bind(null, hs_name));
            } else {
                krpano.set("hotspot[" + hs_name + "].onclick", "js( alert(calc('hotspot \"' + name + '\" clicked')) );");
            }
        }
    }

    //编辑热点
    function editHotspots(hsName) {
        if (krpano.get("device.html5")) {
            var panoId = "{{$panoId}}";
            var temStr = hsName.split("_");
            var scene_name = temStr[0] + "_" + temStr[1];
            var scene_index = krpano.get("scene[" + scene_name + "].index");
            var scene_title = krpano.get("scene[" + scene_name + "].title");

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "{{url('vr/editspot')}}",
                data: {"panoId": panoId, "sceneIndex": scene_index, "hostName": hsName, "sceneName": scene_name},
                success: function (e) {
                    var panoId = e.panoId;
                    var type = e.type;
                    var hsOldName = e.hotsName;
                    var ath = e.ath;
                    var atv = e.atv;
                    var sceneName = e.sceneName;
                    //console.log(panoId,type,hsName,ath,atv,sceneName);

                    var hsNewName = sceneName + '_' + Math.abs((Date.now() + Math.random()) | 0);
                    krpano.call("addhotspot(" + hsNewName + ")");
                    if (type == "point") {
                        krpano.set("hotspot[" + hsNewName + "].url", "{{asset('public/static/hotsport')}}/css/img/hottext_max.png");
                    } else if (type == "hotspot") {
                        krpano.set("hotspot[" + hsNewName + "].url", "{{asset('public/static/hotsport')}}/css/img/hotspot_max.png");
                    }
                    krpano.set("hotspot[" + hsNewName + "].ath", ath);
                    krpano.set("hotspot[" + hsNewName + "].atv", atv);
                    krpano.set("hotspot[" + hsNewName + "].zoom", "true");
                    krpano.set("hotspot[" + hsNewName + "].scale", "0.8");
                    krpano.set("hotspot[" + hsNewName + "].visible", "true");
                    krpano.set("hotspot[" + hsNewName + "].onloaded", "add_all_the_time_tooltip_error(" + hsNewName + ");" +
                        "set(plugin[get(linename)].onclick ,removehotspot(" + hsNewName + ");" +
                        "checkRedErr(" + panoId + "," + scene_index + "," + hsOldName + "," + scene_name + ");" +
                        "set(plugin[get(linename)].visible,false););");
                    krpano.set("hotspot[" + hsNewName + "].ondown", "draghotspot();");
                    if (krpano.get("device.html5") && type == "point") {
                        krpano.set("hotspot[" + hsNewName + "].onclick", function (hs) {
                            var mx_p = krpano.get("mouse.x");
                            var my_p = krpano.get("mouse.y");
                            var pnt_p = krpano.screentosphere(mx_p, my_p);
                            var hh_p = pnt_p.x.toFixed(3);
                            var vv_p = pnt_p.y.toFixed(3);
                            myFun.layer.opens("#titleSetting", "标题标签设置", "small", function (layero) {
                                var _this = this;
                                layero.find(".my-btn-green").click(function () {
                                    var flag = zFun.utils.validationAll();
                                    if (flag) _this.close(index);
                                    var inputTitle = $(".input-point").val();
                                    $.ajax({
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        type: "POST",
                                        url: "{{url('vr/espoint')}}",
                                        data: {
                                            'hsNewName': hsNewName,
                                            'hsOldName': hsOldName,
                                            'h': hh_p,
                                            'v': vv_p,
                                            'sceneTitle': scene_title,
                                            'sceneName': scene_name,
                                            'panoId': panoId,
                                            'sceneIndex': scene_index,
                                            'linkedTitle': inputTitle,
                                        },
                                        success: function (e) {

                                            var hotspotStr = '';
                                            for (var j = 0; j < e.count; j++) {
                                                var hsTemplate = `<div class="table-item">
                                               <div class="table-text table-text1"><p>${e.panoData[j]['sceneName']}</p></div>
                                               <div class="table-text table-text2"><p>${e.panoData[j]['type'] == "point" ? "文本标签" : "场景跳转"}</p></div>
                                               <div class="table-text table-text3"><p>${e.panoData[j]['linkedscene']}</p></div>
                                                    <div class="table-text table-text4">
                                                       <i onclick="editHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconbianji1"></i>
                                                       <i onclick="delHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconshanchu2"></i>
                                                   </div>
                                                </div>`
                                                hotspotStr += hsTemplate;
                                            }
                                            $(".redpoint-count").text("").text(e.count);
                                            $(".hot-manage .my-table").find(".table-content").html("").append(hotspotStr);
                                            krpano.call("loadpano(tour.xml, NULL, MERGE, BLEND(0.1));");
                                            krpano.call("loadscene(" + e.sceneEname + ", NULL, MERGE, BLEND(0.1));");
                                        }
                                    })
                                })

                            });

                        }.bind(null, hsNewName))
                    } else if (krpano.get("device.html5") && type == "hotspot") {
                        krpano.set("hotspot[" + hsNewName + "].onclick", function (hs) {
                            var mx_p = krpano.get("mouse.x");
                            var my_p = krpano.get("mouse.y");
                            var pnt_p = krpano.screentosphere(mx_p, my_p);
                            var hh_p = pnt_p.x.toFixed(3);
                            var vv_p = pnt_p.y.toFixed(3);

                            var count = krpano.get("scene.count");
                            var templateStr = '';
                            for (var i = 0; i < count; i++) {
                                var imgUrl = krpano.get("scene[" + i + "].thumburl");
                                var sceneTitle = krpano.get("scene[" + i + "].title");
                                var template = `<div class="section">
                                        <img src="${imgUrl}" alt="" data="${i}" class="img-child">
                                        <h4 class="title">${sceneTitle}</h4>
                                    </div>`;
                                templateStr += template;
                            }

                            //弹窗
                            myFun.layer.opens("#sceneSetting", "场景跳转设置", "normal", function (layero, index) {
                                var _this = this;
                                layero.find(".img-box").append(templateStr);
                                $(".img-child").click(function (e) {
                                    var event = e || window.event
                                    target = event.target || event.srcElement;
                                    $(target).parents(".img-box").find(".img-border").removeClass("img-border");
                                    $(target).addClass("img-border");
                                    layero.find(".my-input>.inputs").val("");
                                    layero.find(".my-input>.inputs").val($(target).parent(".section").find(".title").text());
                                });
                                layero.find(".my-btn-green").click(function () {
                                    var flag = zFun.utils.validationAll();
                                    var scene_id = $(target).attr("data");
                                    var linkedscene = krpano.get("scene[" + scene_id + "].name");
                                    var linkedTitle = krpano.get("scene[" + scene_id + "].title");
                                    $.ajax({
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        type: "POST",
                                        url: "{{url('vr/esspot')}}",
                                        data: {
                                            'hsNewName': hsNewName,
                                            'hsOldName': hsOldName,
                                            'h': hh_p,
                                            'v': vv_p,
                                            'sceneTitle': scene_title,
                                            'sceneName': linkedscene,
                                            'sceneCurrName': scene_name,
                                            'panoId': panoId,
                                            'sceneIndex': scene_index,
                                            'linkedTitle': linkedTitle,
                                        },
                                        success: function (e) {
                                            console.log(e);
                                            if (flag) _this.close(index);
                                            var hotspotStr = '';
                                            for (var j = 0; j < e.count; j++) {
                                                var hsTemplate = `<div class="table-item">
                                                    <div class="table-text table-text1"><p>${e.panoData[j]['sceneName']}</p></div>
                                                    <div class="table-text table-text2"><p>${e.panoData[j]['type'] == "hotspot" ? "场景跳转" : "文本标签"}</p></div>
                                                    <div class="table-text table-text3"><p>${e.panoData[j]['linkedscene']}</p></div>
                                                     <div class="table-text table-text4">
                                                        <i onclick="editHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconbianji1"></i>
                                                        <i onclick="delHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconshanchu2"></i>
                                                    </div>
                                                </div>`;
                                                hotspotStr += hsTemplate;
                                            }
                                            $(".redpoint-count").text("").text(e.count);
                                            $(".hot-manage .my-table").find(".table-content").html("").append(hotspotStr);

                                            krpano.set("plugin[get(linename)].visible", "false");
                                            krpano.call("loadpano(tour.xml, NULL, MERGE, BLEND(0.1));");
                                            krpano.call("loadscene(" + e.sceneEname + ", NULL, MERGE, BLEND(0.1));");
                                        }
                                    });
                                });
                            });
                        }.bind(null, hsNewName))
                    }
                }
            })
        }
    }

    //删除热点
    function delHotspots(hsName) {
        if (krpano.get("device.html5")) {
            var panoId = "{{$panoId}}";
            var temStr = hsName.split("_");
            var scene_name = temStr[0] + "_" + temStr[1];
            var scene_index = krpano.get("scene[" + scene_name + "].index");
            var scene_title = krpano.get("scene[" + scene_name + "].title");

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "{{url('vr/delspot')}}",
                data: {"panoId": panoId, "sceneIndex": scene_index, "hostName": hsName, "sceneName": scene_name},
                success: function (e) {
                    var panoId = e.panoId;
                    var linkedscene = e.linkedscene;
                    var hsOldName = e.hotsName;
                    var sceneName = e.sceneName;
                    //弹窗
                    const text1 = "【" + scene_title + "】", text2 = "【" + linkedscene + "】";
                    opensHint("删除", "您确定要删除" + text1 + "的" + text2 + "标签 ？", "jc", function (layero, index) {
                        var _this = this;
                        $(layero).find(".my-btn-green").click(function () {
                            _this.close(index)
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                type: "POST",
                                url: "{{url('vr/delhs')}}",
                                data: {
                                    "panoId": panoId,
                                    "sceneIndex": scene_index,
                                    "hostName": hsOldName,
                                    "sceneName": sceneName
                                },
                                success: function (e) {
                                    console.log(e);
                                    var hotspotStr = '';
                                    for (var j = 0; j < e.count; j++) {
                                        var hsTemplate = `<div class="table-item">
                                                    <div class="table-text table-text1"><p>${e.panoData[j]['sceneName']}</p></div>
                                                    <div class="table-text table-text2"><p>${e.panoData[j]['type'] == "hotspot" ? "场景跳转" : "文本标签"}</p></div>
                                                    <div class="table-text table-text3"><p>${e.panoData[j]['linkedscene']}</p></div>
                                                     <div class="table-text table-text4">
                                                        <i onclick="editHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconbianji1"></i>
                                                        <i onclick="delHotspots('${e.panoData[j]['hotsName']}');" class="iconfont iconshanchu2"></i>
                                                    </div>
                                                </div>`;
                                        hotspotStr += hsTemplate;
                                    }
                                    $(".redpoint-count").text("").text(e.count);
                                    $(".hot-manage .my-table").find(".table-content").html("").append(hotspotStr);

                                    krpano.set("plugin[get(linename)].visible", "false");
                                    krpano.call("loadpano(tour.xml, NULL, MERGE, BLEND(0.1));");
                                    krpano.call("loadscene(" + e.sceneEname + ", NULL, MERGE, BLEND(0.1));");
                                }
                            })
                        });
                    });
                }
            })
        }
    }

    //热点隐藏显示切换
    function showHotsport() {
        if (krpano) {
            var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
            var panoId = "{{$panoId}}";
            var sceneName = krpano.get("xml.scene");
            var sceneIndex = krpano.get("scene[get(xml.scene)].index");
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{url('vr/toggle')}}",
                type: "POST",
                data: {"sceneIndex": sceneIndex, "panoId": panoId},
                success: function (res) {
                    // krpano.call("lookat(" + hlookat + "," + vlookat + ",120)");
                    krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                    krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");
                }
            })
        }
    }

    //设置为封面
    function setScene(){
        if (krpano) {
            var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
            var panoId = "{{$panoId}}";
            var sceneName = krpano.get("xml.scene");
            var sceneIndex = krpano.get("scene[get(xml.scene)].index");
            var sceneTitle = krpano.get("scene[" + sceneIndex + "].title");
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{url('vr/setcover')}}",
                type: "POST",
                data: {"sceneIndex": sceneIndex, "panoId": panoId, "sceneTitle": sceneTitle},
                success: function (e) {
                    // console.log(e);
                    $(".isScene").html(e);
                    krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                    krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");
                }
            })
        }
    }

    $(function () {
        $(".setScene1111").on("click", function () {
            if (krpano) {
                var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
                var panoId = "{{$panoId}}";
                var sceneName = krpano.get("xml.scene");
                var sceneIndex = krpano.get("scene[get(xml.scene)].index");
                var sceneTitle = krpano.get("scene[" + sceneIndex + "].title");
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: "{{url('vr/setcover')}}",
                    type: "POST",
                    data: {"sceneIndex": sceneIndex, "panoId": panoId, "sceneTitle": sceneTitle},
                    success: function (e) {
                        // console.log(e);
                        $(".isScene").html(e);
                        krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                        krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");

                    }
                })
            }
        });




        //热点隐藏显示切换
        // function switchHanlder(hason) {
        //     hason ? $(".txt-box").text("展示") : $(".txt-box").text("隐藏");
        // }





        // 场景跳转弹窗
        $(".makeHs122").click(function () {
            myFun.layer.opens("#sceneSetting", "场景跳转设置", "normal", function (layero) {
                $(".img-child").click(function (e) {
                    var event = e || window.event
                    target = event.target || event.srcElement;
                    $(target).parents(".img-box").find(".img-border").removeClass("img-border");
                    $(target).addClass("img-border");
                })
                layero.find(".my-btn-green").click(function () {
                    var flag = zFun.utils.validationAll();
                    console.log(flag);
                })
            });
        });

        // 标题标签设置
        $(".makeHs122").click(function () {
            myFun.layer.opens("#titleSetting", "标题标签设置", "small", function (layero) {
                layero.find(".my-btn-green").click(function () {
                    var flag = zFun.utils.validationAll();
                    console.log(flag);
                })
            });
        });
        // 发布按钮
        // $("#issueBtn").click(function () {
        //     // myFun.layer.opens("#confirmIssue","","small", function() {})
        //     opensHint("发布", "您确定要发布吗？", "jc", function () {
        //         console.log(this);
        //     });
        // });
        // 热点管理删除
        // $(".iconshanchu2").click(function () {
        //     // myFun.layer.opens("#confirmIssue","","small", function() { })
        //     const text1 = "【次卧】", text2 = "【文字】";
        //     opensHint("删除", "您确定要删除" + text1 + "的" + text2 + "标签 ？", "jc", function () {
        //         console.log(this);
        //     });
        // });

    });

    function addLabel(event) {
        console.log(this, event, event.target.innerText);
    }

    /*
     * 温馨提示
     * @parameter htmlText: 显示文本
     * @parameter success: 回调函数
     */
    function opensHint(titleText = "", htmlText = "", alignment = "", success = null) {
        myFun.layer.opens("#cozyHint", titleText, "small", function (layero, index) {
            var _this = this;
            if (alignment) $(layero).find(".cozy-hint").addClass(alignment);
            $(layero).find(".text").html(htmlText);
            success ? success.call(_this, layero, index) : "";
        })
    }


</script>
</html>
