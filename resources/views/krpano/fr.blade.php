<!DOCTYPE html>
<html >
<head>
    <meta charset="utf-8" />
    <title>demo</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="keywords" content="your keywords" />
    <meta name="description" content="your description" />
    <link rel="stylesheet" type="text/css" href="{{asset('public/static/pano')}}/css/p.min.css" />
</head>

<body>
<section class="main vr-index">
    <div class="info-container" >
        <div class="house-info-wrapper" style="display: block">
            <ul class="select-container">
                <li class="select-item active" data-class="select-1">房源信息</li>
                <li class="select-item" data-class="select-2">地图信息</li>
            </ul>
            <ul class="contents-container">
                <li class="content-item select-1">
                    <ul class="text-list">
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">{{$houseInfo->Price}}万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">单价</span>
                            <span class="data">{{ $houseInfo->UnitPrice }}元/m²</span>
                        </li>
                        <li class="text-item">
                            <span class="title">户型</span>
                            <span class="data">{{$houseInfo->CountF}}室{{$houseInfo->CountT}}厅{{$houseInfo->CountW}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">面积</span>
                            <span class="data">{{$houseInfo->ProducingArea}}㎡</span>
                        </li>
                        <li class="text-item">
                            <span class="title">电梯</span>
                            <span class="data">
                                {{ $houseInfo->HasElevator ? '有电梯' : '无电梯' }}
                            </span>
                        </li>
                        <li class="text-item">
                            <span class="title">朝向</span>
                            <span class="data">{{$houseInfo->OrientationName}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">楼层</span>
                            <span class="data">{{$houseInfo->LouCengStr}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">用途</span>
                            <span class="data">{{$houseInfo->PurposeTypeName}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">挂牌</span>
                            <span class="data">{{$houseInfo->ListedTime}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">年代</span>
                            <span class="data">{{$houseInfo->CompletionDate}}</span>
                        </li>
                    </ul>
                    <div class="more-container">
                        <span class="fr_info">更多房源信息</span>
                        <i class="icon-arrow"></i>
                    </div>
                </li>

                <li class="content-item select-2" style="display: none">
                    <div id="map-container" ></div>
                    <div class="more-container">
                        <span class="fr_info">更多房源信息</span>
                        <i class="icon-arrow"></i>
                    </div>
                </li>
            </ul>
        </div>
    </div>

</section>
<footer class="footer"></footer>
<script src="{{asset('public/static/pano')}}/js/jquery-2.1.4.min.js"></script>
<script src="{{asset('public/static/pano')}}/js/rem.js"></script>
<script src="http://api.map.baidu.com/api?v=2.0&ak=990ca087457a9923cc7fd20bbb45b6b9"></script>
<script>
    $(function () {
        frClick();
        mapInit();
        changeTab();
    });

    function frClick(){
        $(".fr_info").click(function () {
            location.href = "http://www.baidu.com";
        })
    }

    function mapInit() {
        var map = new BMap.Map("map-container");
        var point = new BMap.Point(114, 39, 30.501);
        map.centerAndZoom(point, -10);
        var myIcon = new BMap.Icon("{{asset('public/static/pano')}}/css/icon/icon-location.png", new BMap.Size(22, 22), {
            imageSize: new BMap.Size(22, 22)
        });
        var marker = new BMap.Marker(point, {
            icon: myIcon
        })
        map.addOverlay(marker);
    }


    function changeTab() {
        $(".select-container").on('click', '.select-item', function () {
            var $tab = $(this);
            $tab.addClass("active").siblings().removeClass("active");
            var selectedClass = $tab.attr("data-class");
            $(".contents-container").children("." + selectedClass).show().siblings().hide();
        });
    }

</script>
</body>

</html>
