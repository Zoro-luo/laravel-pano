<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="{{asset('public/static/pano/js')}}/tour.js"></script>
    <script src="{{asset('public/static/hotsport')}}/js/jquery-1.8.3.min.js"></script>

    <style>
        @-ms-viewport {
            width: device-width;
        }

        @media only screen and (min-device-width: 800px) {
            html {
                overflow: hidden;
            }
        }

        html {
            height: 100%;
        }

        body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            color: #FFFFFF;
            background-color: #000000;
        }

        #pano {
            margin: 0;
        }

    </style>

</head>
<body>
<div id="pano" style="width:100%; height:100%;">
    <noscript>
        <table style="width:100%;height:100%;">
            <tr style="vertical-align:middle;">
                <td>
                    <div style="text-align:center;">ERROR:<br/><br/>Javascript not activated<br/><br/></div>
                </td>
            </tr>
        </table>
    </noscript>
    <script>

        var gid = "{{$gid}}";
        var sourceType = getQueryVariable("st");
        var flatType = getQueryVariable("ft");
        var houseCode = "{{$houseCode}}";
        var agentCode = "{{$agentCode}}";
        var CityID = "{{$CityID}}";
        var title = "{{$title}}";
        var thumb = "{{$thumb}}";
        var vrUri = window.location.href;

        var agentID = "{{$agentID}}";
        var agentName = "{{$agentName}}";
        var agentPhone = "{{$agentPhone}}";
        var houseID = "{{$houseID}}";
        var houseType = getQueryVariable("ht");

        if (houseType ==2){
            var PhonePosition = 74;
        }else if (houseType ==3){
            var PhonePosition = 75;
        }

        if (sourceType == null) {
            var u = navigator.userAgent;
            var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1 || u.indexOf('Linux') > -1; //android终端
            if (isAndroid) {
                sourceType = 2;
            }
            var isIos = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
            if (isIos) {
                sourceType = 3;
            }
        }

        //在线咨询经纪人
        function LineConsult() {
            document.location = "js://lineconsult?AgentSysCode=" + agentCode + "&AgentName=" + agentName + "&Mobile=" + agentPhone + "&AgentID=" + agentID;
            window.webkit.messageHandlers.lineconsult.postMessage({
                'AgentSysCode': agentCode,
                'AgentName': agentName,
                'Mobile': agentPhone,
                'AgentID': agentID
            });
        }

        //电话联系经纪人
        function Mobile() {
            ClickTel();         //记录埋点
            document.location = "js://mobile?Mobile=" + agentPhone;
            window.webkit.messageHandlers.mobile.postMessage({Mobile: agentPhone});
        }

        function Share_vr() {
            document.location = "js://shareVR";
            window.webkit.messageHandlers.shareVR.postMessage({});
        }

        function Back() {
            document.location = "js://back";
            window.webkit.messageHandlers.back.postMessage({});
        }

        //调客户端的方法解决客户端启动VR的空白页
        function hideHUD() {
            document.location = "js://hideHUD";
            window.webkit.messageHandlers.hideHUD.postMessage({});
        }


        /*var height = ""
        function AdjustTopSpace () {
            //document.location = "js://back";
            window.webkit.messageHandlers.AdjustTopSpace.postMessage({Height:height});
        }*/

        //埋点
        function ClickTel() {
            var url = "http://flume.t.jjw.com/api/Shunt/Index";
            var data = {
                Data: '{"AgentID":' + agentID + ',"AgentMobile":"' + agentPhone + '","HouseID":' + houseID + ',"PhonePosition":' + PhonePosition + ',"HouseSysCode":"' + houseCode + '"}',
                DataType: 3
            };
            setTimeout(function () {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: "POST",
                    dataType: 'json',
                    async: true,
                    url: url + "?r=" + new Date().getTime(),
                    data: data,
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function (e) {
                        return;
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        return;
                    }
                });
            }, 10);
        }



        //分享标题
        function shareTitle() {
            return title;
        }

        //分享描述文本
        function shareDescriptions() {
            return "看房不出门，一触即到!";
        }

        //分享标题图片
        function shareTitleImage() {
            return thumb;
        }

        //分享落地页
        /*function shareUrl() {
            //return vrUri;
        }*/

        //获取url参数
        function getQueryVariable(variable) {
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                if (pair[0] == variable) {
                    return pair[1];
                }
            }
            return (false);
        }

        var krpano = null;
        var sign = null;


        var xmlPath = "{{asset('storage/panos/').'/'.$gid }}/vtour/tour_pro.xml";

        $(function () {
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: xmlPath,
                type: "HEAD",
                success: function () {
                    //console.log("存在");
                    embedpano({
                        swf: "{{asset('storage/panos/').'/'.$gid }}/vtour/tour.swf",
                        id: "krpanoSWFObject",
                        xml: xmlPath,
                        target: "pano",
                        passQueryParameters: true,
                        onready: krpano_onready_callback,
                    });

                    function krpano_onready_callback(krpano_interface) {
                        krpano = krpano_interface;
                    }
                },
                error: function () {
                    //console.log("不存在");
                    embedpano({
                        swf: "{{asset('storage/panos/').'/'.$gid }}/vtour/tour.swf",
                        id: "krpanoSWFObject",
                        xml: "{{asset('storage/panos/').'/'.$gid }}/vtour/tour.xml",
                        target: "pano",
                        passQueryParameters: true,
                        onready: krpano_onready_callback,
                    });

                    function krpano_onready_callback(krpano_interface) {
                        krpano = krpano_interface;
                    }
                }
            });
        })


    </script>
</div>

</body>
</html>



