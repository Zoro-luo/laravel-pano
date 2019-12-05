<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="keywords" content="your keywords" />
    <meta name="description" content="your description" />
    <link rel="stylesheet" type="text/css" href="{{asset('public/static/pano')}}/css/p.vr.css" />
</head>

<body>
<section class="main vr-index">
    <!-- 内容展示 -->
    <div class="info-container" >
        <div class="info-wrapper" style="display: block">
            <div class="infos">
                <div class="info-top">
                    <div class="avatar-container">
                        <div class="avatar-wrapper">
                            <img src="{{$agentInfo =="NULL" ? "NULL" : $agentInfo->ImageUrl}}" alt="">
                        </div>
                    </div>
                    <div class="details-container">
                        <div class="d-top">
                            <h3 class="name">{{$agentInfo == "NULL" ? "NULL" : $agentInfo->AgentName}}</h3>
                            <span class="company">{{$agentInfo == "NULL" ? "NULL" : $agentInfo->BrandMark}}</span>
                        </div>
                        <div class="d-bottom">
                            <p class="text">
                                所属门店：{{$agentInfo == "NULL" ? "NULL" : $agentInfo->StoreName}}
                            </p>
                        </div>
                    </div>
                </div>
                <ul class="info-middle">
                    <li class="m-item">
                        <span class="title">成交</span>
                        <h3 class="data">{{$agentInfo == "NULL" ? "NULL" : $agentInfo->EsfDealNum}}</h3>
                    </li>
                    <li class="m-item">
                        <span class="title">带看</span>
                        <h3 class="data">{{$agentInfo == "NULL" ? "NULL" : $agentInfo->EsfSeeNum}}</h3>
                    </li>
                    <li class="m-item">
                        <span class="title">粉丝</span>
                        <h3 class="data">{{$agentInfo == "NULL" ? "NULL" : $agentInfo->Fans}}</h3>
                    </li>
                </ul>
                <div class="info-bottom">
                        <span class="btn">
                            <a href="http://www.baidu.com">更多经纪人信息</a>
                        </span>
                </div>
            </div>
        </div>

    </div>

</section>
<footer class="footer"></footer>
<script src="{{asset('public/static/pano')}}/js/jquery-2.1.4.min.js"></script>
<script src="{{asset('public/static/pano')}}/js/rem.js"></script>
{{--<script>
    $(function () {
        vrInit();
        selectTab();
        mapInit();
        toggleAgent();
        toggleHouseInfo();
    });

    function selectTab() {
        $('.house-info-wrapper .select-2').hide();
    }

    function mapInit() {
        var map = new BMap.Map("map-container");
        var point = new BMap.Point(114,39, 30.501);
        map.centerAndZoom(point,15);
        var myIcon = new BMap.Icon('./css/icon/icon-location.png',new BMap.Size(22,22),{
            imageSize: new BMap.Size(22,22)
        });
        var marker = new BMap.Marker(point,{
            icon: myIcon
        })
        map.addOverlay(marker);
    }

    function vrInit() {
        $('.info-container').children().hide();
    }
    function checkHeader() {
        var activeBoolen = $('.info-container').children().every(function (i,item) {
            return $(item).is(":hidden");
        });
        if (activeBoolen) {
            $(".main .header").removeClass('active');
        }else{
            $(".main .header").addClass('active');
        }
    }

    function toggleAgent() {
        $('.agent-container .avatar-container').click(function () {
            var $elem = $('.info-container .info-wrapper');
            if ($elem.is(":visible")) {
                vrInit();
            }else{
                vrInit();
                $elem.show();
            }
            checkHeader();
        });
    }
    function toggleHouseInfo() {
        $('.header .c-wrapper .icon-head-arrow').click(function () {
            var $elem = $('.info-container .house-info-wrapper');
            if ($elem.is(":visible")) {
                vrInit();
            }else{
                vrInit();
                $elem.show();
            }
            checkHeader();
        });
    }

    $.fn.every = function (callback) {
        var result = true;
        var self = this;
        this.each(function (i,item) {
            if (!callback(i,item,self)) {
                result = false;
                return false;
            }
        });
        return result;
    }



</script>--}}
</body>

</html>
