<html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="keywords" content="your keywords" />
    <meta name="description" content="your description" />
    <script src="{{asset('public/static/pano')}}/js/jquery-2.1.4.min.js"></script>
    <script src="{{asset('public/js')}}/jquery.qrcode.min.js"></script>

    <style>
        .back_img{
            color: #fff;
            text-align: center;
            font-size: 14px;
            margin-top: 16px;
        }
        .back_img p{
            margin: 11px 0;
        }
        #qrcode{
            width: 140px;
            height: 140px;
            display: inline-block;
        }
    </style>

    <script type="text/javascript">
        jQuery(function(){
            jQuery('#qrcode').qrcode({
                render: "canvas",
                width: 140,
                height: 140,
                text: "{{ $agentInfo=="NULL" ? "NULL" : $agentInfo->AgentName }}",
            });
        })
    </script>

</head>
<body>
<div class="back_img">
    <div id="qrcode"></div>
    <p>微信扫码分享</p>
</div>

</body>


</html>
