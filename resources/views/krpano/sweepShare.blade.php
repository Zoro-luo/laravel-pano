<html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0, user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="keywords" content="your keywords"/>
    <meta name="description" content="your description"/>
    <script src="{{asset('public/static/pano')}}/js/jquery-2.1.4.min.js"></script>
    <script src="{{asset('public/js')}}/jquery.qrcode.min.js"></script>

    <style>
        .back_img {
            color: #fff;
            text-align: center;
            font-size: 14px;
            margin-top: 16px;
        }

        .back_img p {
            margin: 11px 0;
        }

        #qrcode {
            width: 140px;
            height: 140px;
            display: inline-block;
        }
    </style>

    <script type="text/javascript">
        //解析获取地址参数 json格式化
        function parseQuery(url) {
            let o = {}
            let queryString = url.split('?')[1]
            if (queryString) {
                queryString
                    .split('&')
                    .forEach(item => {
                        let [key, val] = item.split('=')
                        val = val ? decodeURI(val) : true
                        //          转码         无值赋值true
                        if (o.hasOwnProperty(key)) {
                            //   已有属性转为数组
                            o[key] = [].concat(o[key], val)
                        } else {
                            o[key] = val
                        }
                    })
            }
            return o
        }


        jQuery(function () {
            var panoUrl = "{{$panoUrl}}";
            var jsonUrl = parseQuery(panoUrl);
            console.log(jsonUrl);
            var ac = jsonUrl["ac"];
            var cs = jsonUrl["amp;cs"];
            var ft = jsonUrl["amp;ft"];
            var hc = jsonUrl["amp;hc"];
            var ht = jsonUrl["amp;ht"];

            var strs = new Array();
            strs = panoUrl.split("?");
            var urlHost = strs[0];
            var url = urlHost + "?ac=" + ac + "&cs=" + cs + "&ft=" + ft + "&hc=" + hc + "&ht=" + ht;

            jQuery('#qrcode').qrcode({
                render: "canvas",
                width: 140,
                height: 140,
                text: url,
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
