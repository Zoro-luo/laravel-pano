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
    <link rel="stylesheet" type="text/css" href="{{asset('public/static/pano')}}/css/pc.min.css" media="screen and (min-width:1025px)">
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
                            <img src="{{$agentInfo =="" ? "" : $agentInfo->ImageUrl}}" alt="">
                        </div>
                    </div>
                    <div class="details-container">
                        <div class="d-top">
                            <h3 class="name">{{$agentInfo == "" ? "" : $agentInfo->AgentName}}</h3>
                            <span class="company">{{$agentInfo == "" ? "" : $agentInfo->BrandName}}</span>
                        </div>
                        <div class="d-bottom">
                            <p class="text">
                                所属门店：{{$agentInfo == "" ? "" : $agentInfo->StoreName}}
                            </p>
                        </div>
                    </div>
                </div>
                <ul class="info-middle clearfix">
                    <li class="m-item">
                        <span class="title">成交</span>
                        <h3 class="data">{{$agentInfo == "" ? "" : $agentInfo->TransactionCount}}</h3>
                    </li>
                    <li class="m-item">
                        <span class="title">带看</span>
                        <h3 class="data">{{$agentInfo == "" ? "" : $agentInfo->AgentEvaluateNum}}</h3>
                    </li>
                    <li class="m-item">
                        <span class="title">粉丝</span>
                        <h3 class="data">{{$agentInfo == "" ? "" : $agentInfo->Fans}}</h3>
                    </li>
                </ul>
                <div class="info-bottom">
                        <span class="btn">
                            更多经纪人信息
                        </span>
                </div>
            </div>
        </div>

    </div>

</section>
<footer class="footer"></footer>
<script src="{{asset('public/static/pano')}}/js/jquery-2.1.4.min.js"></script>
<script src="{{asset('public/static/pano')}}/js/rem.js"></script>
<script>
    $(function () {
        frClick();
    });
    function frClick() {
        $(".btn").click(function () {
            window.open("{{$agentInfo->SiteUrl}}");
        })
    }
</script>
</body>

</html>
