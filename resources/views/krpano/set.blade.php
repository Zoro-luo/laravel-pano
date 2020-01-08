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
    <link rel="stylesheet" type="text/css" href="{{asset('public/static/pano')}}/css/p.vr.css" />
    <link rel="stylesheet" type="text/css" href="{{asset('public/static/pano')}}/css/pc.min.css" media="screen and (min-width:1025px)">
</head>

<body>
<section class="main vr-index">

    <div class="setting-wrapper cm tc">
        <p class="title">设置</p>
        <div class="row clearfix">
            <span class="fl">陀1螺仪</span>
            <i class="icon-switch fr"></i>
        </div>
        <div class="row clearfix">
            <span class="fl">自动旋转</span>
            <i class="icon-switch fr active"></i>
        </div>
    </div>

</section>
<footer class="footer"></footer>
<script src="{{asset('public/static/pano')}}/js/jquery-2.1.4.min.js"></script>
<script src="{{asset('public/static/pano')}}/js/rem.js"></script>
<script src="http://api.map.baidu.com/api?v=2.0&ak=990ca087457a9923cc7fd20bbb45b6b9"></script>
<script>

    $(function () {
        $(".icon-switch").click(function () {
            if ($(this).hasClass("active")){
                $(this).removeClass("active")
            }else{
                $(this).addClass("active")
            }
        });
    })


</script>
</body>

</html>
