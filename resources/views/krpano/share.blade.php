<!DOCTYPE html>
<html>

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
{{--<section class="main hotsport-index">
    <!-- 头部 -->
    <header class="header show">
        <a href="javascript:;" class="h-left">
            <i class="icon-head-back"></i>
        </a>
        <div class="h-center">
            <div class="c-wrapper">
                <h3 class="title">
                    国际百纳 3室2厅凑十二字十二字...
                </h3>
                <i class="icon-head-arrow"></i>
            </div>
        </div>
        <span class="h-right">
                <i class="icon-head-share"></i>
            </span>
    </header>
    <!-- 按钮 -->
    <div class="btn-container">
        <i class="btn icon-hotsport"></i>
        <i class="btn icon-setting"></i>
    </div>
    <!-- 内容展示 -->
    <div class="info-container">
        <div class="info-wrapper">
            <div class="infos">
                <div class="info-top">
                    <div class="avatar-container">
                        <div class="avatar-wrapper">
                            <img src="{{asset('public/static/pano')}}/images/agent-photo.png" alt="">
                        </div>
                    </div>
                    <div class="details-container">
                        <div class="d-top">
                            <h3 class="name">李贤希</h3>
                            <span class="company">JJW·吉家</span>
                        </div>
                        <div class="d-bottom">
                            <p class="text">
                                所属门店：光谷云座一店
                            </p>
                        </div>
                    </div>
                </div>
                <ul class="info-middle">
                    <li class="m-item">
                        <span class="title">成交</span>
                        <h3 class="data">258</h3>
                    </li>
                    <li class="m-item">
                        <span class="title">成交</span>
                        <h3 class="data">258</h3>
                    </li>
                    <li class="m-item">
                        <span class="title">成交</span>
                        <h3 class="data">258</h3>
                    </li>
                </ul>
                <div class="info-bottom">
                        <span class="btn">
                            更多经纪人信息
                        </span>
                </div>
            </div>
        </div>
        <div class="house-info-wrapper">
            <ul class="select-container">
                <li class="select-item active" data-class="select-1">房源信息</li>
                <li class="select-item" data-class="select-2">地图信息</li>
            </ul>
            <ul class="contents-container">
                <li class="content-item select-1">
                    <ul class="text-list">
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">单价</span>
                            <span class="data">26598元/m²26598元/m²</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元元元500万元元元</span>
                        </li>
                        <li class="text-item">
                            <span class="title">售价</span>
                            <span class="data">500万元</span>
                        </li>
                    </ul>
                    <div class="more-container">
                        <span>更多房源信息</span>
                        <i class="icon-arrow"></i>
                    </div>
                </li>
                <li class="content-item select-2">
                    <div id="map-container"></div>
                    <div class="more-container">
                        <span>更多房源信息</span>
                        <i class="icon-arrow"></i>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!-- 底部经纪人 -->
    <div class="agent-container">
        <div class="logo-container">
            <i class="icon-logo"></i>
        </div>
        <div class="agent-wrapper">
            <div class="avatar-container">
                <img src="{{asset('public/static/pano')}}/images/agent-photo.png" alt="">
                <i class="icon-corner-mark"></i>
            </div>
            <i class="icon-im"></i>
            <i class="icon-phone"></i>
            <span class="select-box">
                    客厅
                    <i class="icon-arrow"></i>
                </span>
        </div>
    </div>
</section>--}}
<!-- 分享 -->


<div class="share-container vr-index" style="display: block">
    <div class="share-wrapper">
        <ul class="share-links">
            <li class="item-link">
                <i class="icon wechat"></i>
                <span>微信好友</span>
            </li>
            <li class="item-link">
                <i class="icon friend"></i>
                <span>朋友圈</span>
            </li>
            <li class="item-link">
                <i class="icon copy-link"></i>
                <span>复制链接</span>
            </li>
        </ul>
        <div class="btn-cancel">
            取消
        </div>
    </div>
</div>
<footer class="footer"></footer>
<script src="{{asset('public/static/pano')}}/js/jquery-2.1.4.min.js"></script>
<script src="{{asset('public/static/pano')}}/js/rem.js"></script>
<script src="{{asset('public/static/pano')}}/js/tour.js"></script>
<div id="pano">
<script>



    $(function () {
        sharePage();
    });

    function sharePage() {
        $('.share-container .btn-cancel').click(function () {

           /* embedpano({swf:"{{storage_path('panoImg/2/')}}}vtour/tour.swf", xml:"{{storage_path('panoImg/2/')}}vtour/tour.xml", target:"pano", html5:"auto", mobilescale:1.0, passQueryParameters:true});
            var krpano = document.getElementById('krpanoSWFObject');
            krpano.call("show_control_bar()");*/

            $('.share-container').hide();
        });
    }


</script>
</div>
</body>

</html>
