<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>demo</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0, user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="keywords" content="your keywords"/>
    <meta name="description" content="your description"/>
    <link rel="stylesheet" type="text/css" href="{{asset('public/static/pano')}}/css/p.vr.css"/>
    <link rel="stylesheet" type="text/css" href="{{asset('public/static/pano')}}/css/pc.min.css"
          media="screen and (min-width:1025px)">
</head>

<body>
<section class="main vr-index">
    <div class="info-container">
        <div class="house-info-wrapper" style="display: block">
            <ul class="select-container clearfix">
                <li class="select-item active" data-class="select-1">房源信息</li>
                <li class="select-item" data-class="select-2">地图信息</li>
            </ul>
            <ul class="contents-container">
                <li class="content-item select-1">
                    <ul class="text-list clearfix">
                        <li class="text-item">
                            <span class="title">售价</span>

                            <span class="data">{{$houseInfo->Price}}{{$houseInfo->PriceUnit}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">单价</span>
                            <span class="data">{{$houseInfo->UnitPrice}}{{$houseInfo->UnitPriceUnit}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">户型</span>
                            <span class="data">{{$houseInfo =="" ? "" : $houseInfo->CountF."室"}}{{$houseInfo =="" ? "" : $houseInfo->CountT."厅"}}{{$houseInfo =="" ? "" : $houseInfo->CountW."卫"}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">面积</span>
                            <span class="data">{{$houseInfo =="" ? "" : $houseInfo->ProducingArea."㎡"}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">电梯</span>
                            <span class="data">
                                {{$houseInfo->HasElevatorName}}
                                {{-- {{ $houseInfo->HasElevator ? '有电梯' : '无电梯' }}--}}
                            </span>
                        </li>
                        <li class="text-item">
                            <span class="title">朝向</span>
                            <span class="data">{{$houseInfo =="" ? "" : $houseInfo->OrientationName}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">楼层</span>
                            <span class="data">{{$houseInfo =="" ? "" : $houseInfo->LouCengStr}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">用途</span>
                            <span class="data">{{$houseInfo =="" ? "" : $houseInfo->PurposeTypeName}}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">挂牌</span>
                            <span class="data">{{$houseInfo =="" ? "" :  date('Y.m.d',strtotime($houseInfo->ListedTime)) }}</span>
                        </li>
                        <li class="text-item">
                            <span class="title">年代</span>
                            <span class="data">{{$houseInfo =="" ? "" : $houseInfo->CompletionDate}}</span>
                        </li>
                    </ul>
                    <div class="more-container">
                        <span class="fr_info">更多房源信息</span>
                        <i class="icon-arrow"></i>
                    </div>
                </li>

                <li class="content-item select-2" style="display: none">
                    <div id="map-container"></div>
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
        changeTab();
    });

    function frClick() {
        $(".fr_info").click(function () {
            let ua = navigator.userAgent //用户代理头信息
            let isAnd = ua.indexOf("originFromAndWebView") > -1;
            let isIos = ua.indexOf("JiJia-Client-iOS") > -1;
            //判断是否APP
            if (isAnd || isIos) {
                //console.log('app')
                let ft = "{{$houseInfo->flagType}}";
                if ( ft == '1') {       //B端
                    let hc = "{{$houseInfo->SysCode}}";
                    if (isIos) {
                        window.webkit.messageHandlers.jumpToPropertyDetail.postMessage({'Code':hc}) //iOS
                    } else {
                        window.location = `js://jumpToPropertyDetail?Code=${hc}` //Android
                    }
                } else {
                    //console.log('c')
                    let id = "{{$houseInfo->ID}}";      //房源id
                    let housetype = "{{$houseInfo->HouseType}}";    //房源类型
                    let purposetype = "{{$houseInfo->PurposeType}}";    //用途
                    if (isIos) {
                        window.webkit.messageHandlers.linktohousedetail.postMessage({
                            id,
                            housetype,
                            purposetype,
                        }) //iOS
                    } else {
                        window.location =
                            `js://linktohousedetail?id=${id}&housetype=${housetype}&purposetype=${purposetype}` //Android
                    }
                }
            } else {
                //console.log('不是app')
                //window.open("{{$houseInfo->SiteUrl}}");
                top.location.href = "{{$houseInfo->SiteUrl}}";
            }

        })
    }

    function mapInit() {
        var Longitude = "{{$houseInfo->Longitude}}";
        var Latitude = "{{$houseInfo->Latitude}}";
        var _map = new BMap.Map("map-container");
        var pt = new BMap.Point(Longitude, Latitude);
        _map.centerAndZoom(pt, 12);
        //var mapicon = new BMap.Icon("{{asset('public/static/pano')}}/css/icon/icon-location.png", new BMap.Size(50, 50));
        var mapicon = new BMap.Icon("{{asset('public/static/pano')}}/css/icon/icon-location.png", new BMap.Size(30, 30), {
            imageSize: new BMap.Size(30, 30)
        });
        var marker = new BMap.Marker(pt, {icon: mapicon});
        _map.addOverlay(marker);
        marker.disableMassClear();
        //_map.disableDragging();

    }


    function changeTab() {
        $(".select-container").on('click', '.select-item', function () {
            var $tab = $(this);
            $tab.addClass("active").siblings().removeClass("active");
            var selectedClass = $tab.attr("data-class");
            $(".contents-container").children("." + selectedClass).show().siblings().hide();
            if ($tab.attr("data-class") == "select-2") {
                mapInit();
            }
        });
    }

</script>
</body>

</html>
