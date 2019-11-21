<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>vr-详情页</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('public/static/hotsport')}}/css/p.min.css">
    <script src="{{asset('public/static/pano/js')}}/tour.js"></script>
</head>
<body>
<div class="header"></div>
<div class="container">
    <div class="vr-container mt-15">
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
                    <div class="my-btn my-btn-green set-covers setScene">设置为封面图</div>
                    <span>已设置：厨房</span>
                </div>
                <div class="set-hot-choice section">
                    <h3 class="s-title">热点选择</h3>
                    <div class="hot-label">
                        <p>场景跳转标签</p>
                        <ul>
                            <li class="gopng_red-point_outer"><img onclick="addHotspots();" src="{{asset('public/static/hotsport')}}/css/icon/red-point.png" alt=""></li>
                        </ul>
                    </div>
                    <div class="hot-label">
                        <p>文字标签</p>
                        <ul>
                            <li class="gopng_font-label_outer"><img src="{{asset('public/static/hotsport')}}/css/icon/font-label.png" alt=""></li>
                        </ul>
                    </div>
                </div>
                <div class="hot-show section">
                    <h3 class="s-title">热点展示</h3>
                    <div class="swich-box">
                        <label for="" @click-list="switchHanlder"><input type="checkbox" name="" id="" class="a-switch showHotsport"></label>
                        <span class="txt-box">隐藏</span>
                    </div>
                </div>
                <div class="hot-manage section">
                    <h3 class="s-title">热点管理<span>（33个）</span></h3>
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
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>场景跳转</p></div>
                                    <div class="table-text table-text3"><p>客厅</p></div>
                                    <div class="table-text table-text4"><i class="iconfont iconbianji1"></i><i
                                                class="iconfont iconshanchu2"></i></div>
                                </div>
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
                        <div class="section">
                            <img src="./images/pool.jpg" alt="" class="img-child">
                            <h4 class="title">客厅</h4>
                        </div>
                        <div class="section">
                            <img src="./images/1.png" alt="" class="img-child">
                            <h4 class="title">餐厅</h4>
                        </div>
                        <div class="section">
                            <img src="./images/pool.jpg" alt="" class="img-child">
                            <h4 class="title">主卧</h4>
                        </div>
                        <div class="section">
                            <img src="./images/1.png" alt="" class="img-child">
                            <h4 class="title">次卧</h4>
                        </div>
                        <div class="section">
                            <img src="./images/pool.jpg" alt="" class="img-child">
                            <h4 class="title">厨房</h4>
                        </div>
                        <div class="section">
                            <img src="./images/1.png" alt="" class="img-child">
                            <h4 class="title">卫生间</h4>
                        </div>
                        <div class="section">
                            <img src="./images/pool.jpg" alt="" class="img-child">
                            <h4 class="title">客厅</h4>
                        </div>
                        <div class="section">
                            <img src="./images/1.png" alt="" class="img-child">
                            <h4 class="title">客厅</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-list">
            <div class="flex-left item-center"><span class="r-hint">*</span>标签名称</div>
            <div class="separator item-center">:</div>
            <div class="flex-right">
                <div class="my-input"><input type="text" class="inputs monitor" placeholder="请输入5个中文字以内" maxlength="5"></div>
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
                <div class="my-input"><input type="text" class="inputs monitor" placeholder="请输入10个中文字以内" maxlength="10"></div>
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
{{--<script src="{{asset('public/static/hotsport')}}/js/layer/layer.js"></script>--}}
{{--<script src="{{asset('public/static/hotsport')}}/js/common.js"></script>--}}
{{--<script src="{{asset('public/static/hotsport')}}/js/zxc_common.js"></script>--}}
<!-- <script src="js/laydate/laydate.js"></script> -->
<!-- <script src="js/echarts.simple.min.js"></script> -->
<script>
    //添加热点
    function addHotspots() {
        // alert(icon)
        if (krpano) {
            var h = krpano.get("view.hlookat").toFixed(3);
            var v = krpano.get("view.vlookat").toFixed(3);
            var sceneName = krpano.get("xml.scene");
            var sceneIndex = krpano.get("scene[get(xml.scene)].index");
            var panoId = "{{$panoId}}";
            var hs_name = sceneName + '_' + Math.abs((Date.now() + Math.random()) | 0);
            krpano.call("addhotspot(" + hs_name + ")");
            if (icon == "u104") {
                krpano.set("hotspot[" + hs_name + "].url", "{{asset('storage/panos/').'/'.$panoId}}/vtour/skin/u104.svg");
            } else if (icon == "u31") {
                krpano.set("hotspot[" + hs_name + "].url", "{{asset('storage/panos/').'/'.$panoId}}/vtour/skin/u31.svg");
            } else if (icon == "u26") {
                krpano.set("hotspot[" + hs_name + "].url", "{{asset('storage/panos/').'/'.$panoId}}/vtour/skin/u26.svg");
            } else {
                krpano.set("hotspot[" + hs_name + "].url", "{{asset('storage/panos/').'/'.$panoId}}/vtour/skin/vtourskin_hotspot.png");
            }

            krpano.set("hotspot[" + hs_name + "].ath", h);
            krpano.set("hotspot[" + hs_name + "].atv", v);
            krpano.set("hotspot[" + hs_name + "].zoom", "true");
            krpano.set("hotspot[" + hs_name + "].scale", "0.45");
            krpano.set("hotspot[" + hs_name + "].visible", "true");
            krpano.set("hotspot[" + hs_name + "].onhover", "draghotspot();");
        }
    }



    $(function () {
        //设置为封面
        $(".setScene").on("click", function () {
            if (krpano) {

                var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
                var panoId = "{{$panoId}}";
                var sceneName = krpano.get("xml.scene");
                var sceneIndex = krpano.get("scene[get(xml.scene)].index");
                var sceneTitle = krpano.get("scene[" + sceneIndex + "].title");
                console.log(sceneIndex);
                console.log(sceneTitle);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{url('/admin/setcover')}}",
                    type: "POST",
                    data: {"sceneIndex": sceneIndex, "panoId": panoId, "sceneTitle": sceneTitle},
                    success: function (e) {
                         console.log(e);
                        // krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                        // krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");
                        // $(".setCover").html(e)
                    }
                })
            }
        });

        //热点展示
        $(".showHotsport").click(function () {
            if (krpano) {

                var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
                var panoId = "{{$panoId}}";
                var sceneName = krpano.get("xml.scene");
                var sceneIndex = krpano.get("scene[get(xml.scene)].index");

                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: "{{url('admin/toggleHs')}}",
                    type: "POST",
                    data: {"sceneIndex": sceneIndex, "panoId": panoId},
                    success: function (res) {
                        // krpano.call("lookat(" + hlookat + "," + vlookat + ",120)");
                        krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                        krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");
                    }
                })

            }
        });


        // 场景跳转弹窗
        // myFun.layer.opens("#sceneSetting","场景跳转设置","normal", function(layero) {
        //     $(".img-child").click(function(e) {
        //         var event = e || window.event
        //             target = event.target || event.srcElement;
        //         $(target).parents(".img-box").find(".img-border").removeClass("img-border");
        //         $(target).addClass("img-border");
        //     })
        //     layero.find(".my-btn-green").click(function() {
        //         var flag = zFun.utils.validationAll();
        //         console.log(flag);
        //     })
        // });
        // 标题标签设置
        // myFun.layer.opens("#titleSetting","标题标签设置","small", function(layero) {
        //     layero.find(".my-btn-green").click(function() {
        //         var flag = zFun.utils.validationAll();
        //         console.log(flag);
        //     })
        // });
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

    /*
     * 温馨提示
     * @parameter htmlText: 显示文本
     * @parameter success: 回调函数
     */
    // function opensHint(titleText = "", htmlText = "", alignment = "", success = null) {
    //     myFun.layer.opens("#cozyHint", titleText, "small", function (layero, index) {
    //         if (alignment) $(layero).find(".cozy-hint").addClass(alignment);
    //         $(layero).find(".text").html(htmlText);
    //         success ? success.call(layero) : "";
    //     })
    // }

    // function switchHanlder(hason) {
    //     hason ? $(".txt-box").text("展示") : $(".txt-box").text("隐藏");
    // }
</script>
</html>
