<!DOCTYPE html>
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
<div class="container mt-15">
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
                <div class="set-cover section">
                    <div class="my-btn my-btn-green setScene">设置为封面图</div>
                    <span>（已设置：<span class="setCover"></span>）</span>
                </div>
                <div class="set-hot-choice section">
                    <h3 class="s-title">热点选择</h3>
                    <div class="hot-label">
                        <p>场景跳转标签</p>
                        <ul>
                            <li><img onclick="addHotspots('u104');"
                                     src="{{asset('public/static/hotsport')}}/css/icon/u104.svg" alt=""></li>
                            <li><img onclick="addHotspots('u31');"
                                     src="{{asset('public/static/hotsport')}}/css/icon/u31.svg" alt=""></li>
                            <li><img onclick="addHotspots('u26');"
                                     src="{{asset('public/static/hotsport')}}/css/icon/u26.svg" alt=""></li>
                        </ul>
                    </div>
                    <div class="hot-label">
                        <p>文字标签</p>
                        <ul>
                            <li><img onclick="addHotspots('u106');"
                                     src="{{asset('public/static/hotsport')}}/css/icon/u106.png" alt=""></li>
                            <li><img onclick="addHotspots('u29');"
                                     src="{{asset('public/static/hotsport')}}/css/icon/u29.svg" alt=""></li>
                            <li><img onclick="addHotspots('u30');"
                                     src="{{asset('public/static/hotsport')}}/css/icon/u30.svg" alt=""></li>
                        </ul>
                    </div>
                </div>
                <div class="hot-show section">
                    <h3 class="s-title">热点展示</h3>
                    <label for=""><input type="checkbox" name="" id="" class="a-switch showHotsport"></label>
                </div>
                <div class="hot-manage section">
                    <h3 class="s-title">热点管理</h3>
                    <div class="scroll-box">
                        <div class="my-table table-4 table-nomral">
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
                                <div class="table-text table-text2">序号</div>
                                <div class="table-text table-text3">热点类型</div>
                                <div class="table-text table-text4">操作</div>
                            </div>
                            <div class="table-content">
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>7</p></div>
                                    <div class="table-text table-text3"><p>场景跳转</p></div>
                                    <div class="table-text table-text4"><p></p></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>7</p></div>
                                    <div class="table-text table-text3"><p>场景跳转</p></div>
                                    <div class="table-text table-text4"><p></p></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>7</p></div>
                                    <div class="table-text table-text3"><p>场景跳转</p></div>
                                    <div class="table-text table-text4"><p></p></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>7</p></div>
                                    <div class="table-text table-text3"><p>场景跳转</p></div>
                                    <div class="table-text table-text4"><p></p></div>
                                </div>
                                <div class="table-item">
                                    <div class="table-text table-text1"><p>厨房</p></div>
                                    <div class="table-text table-text2"><p>7</p></div>
                                    <div class="table-text table-text3"><p>场景跳转</p></div>
                                    <div class="table-text table-text4"><p></p></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-btn">预览</div>
                <div class="my-btn my-btn-green">确认发布</div>
            </div>
        </div>
    </div>
</div>
</body>
<script src="{{asset('public/static/hotsport')}}/js/jquery-1.8.3.min.js"></script>
<!-- <script src="js/layer/layer.js"></script> -->
<script src="{{asset('public/static/hotsport')}}/js/common.js"></script>
<!-- <script src="js/laydate/laydate.js"></script> -->
<!-- <script src="js/echarts.simple.min.js"></script> -->
<script>

    //添加热点
    function addHotspots(icon) {
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

        if (krpano) {

            // var x = krpano.get("mouse.x").toFixed(3);
            // var y = krpano.get("mouse.y").toFixed(3);
            // var stagex = krpano.get("mouse.stagex").toFixed(3);
            // var stagey = krpano.get("mouse.stagey").toFixed(3);
            //
            // var ath = krpano.get("curscreen_ath");
            // var atv = krpano.get("curscreen_atv");



        }

        //设置为封面
        $(".setScene").on("click", function () {
            if (krpano) {
                var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
                var panoId = "{{$panoId}}";
                var sceneName = krpano.get("xml.scene");
                var sceneIndex = krpano.get("scene[get(xml.scene)].index");
                var sceneTitle = krpano.get("scene[" + sceneIndex + "].title");
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: "{{url('admin/setcover')}}",
                    type: "POST",
                    data: {"sceneIndex": sceneIndex, "panoId": panoId, "sceneTitle": sceneTitle},
                    success: function (e) {
                        // console.log(e);
                        krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                        krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");
                        $(".setCover").html(e)
                    }
                })
            }
        });

        //热点展示
        $(".showHotsport").on("click", function () {
            if (krpano) {
                var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
                var panoId = "{{$panoId}}";
                var sceneName = krpano.get("xml.scene");
                var sceneIndex = krpano.get("scene[get(xml.scene)].index");

                //var hlookat = krpano.get("view.hlookat").toFixed(3);
                //var vlookat = krpano.get("view.vlookat").toFixed(3);

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
        })

    })


</script>
</html>
