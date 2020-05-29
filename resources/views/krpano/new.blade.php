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
        var krpano = null;
        var sign = null;

        ///var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
        var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour_pro.xml";
        console.log(xmlPath);

        $(function () {
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:xmlPath,
                type:"HEAD",
                success:function () {
                    //console.log("存在");
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
                },
                error:function () {
                    //console.log("不存在");
                    embedpano({
                        swf: "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.swf",
                        id: "krpanoSWFObject",
                        xml: "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml",
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




        /*embedpano({
            swf: "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.swf",
            id: "krpanoSWFObject",
            xml: xmlPath,
            target: "pano",
            passQueryParameters: true,
            onready: krpano_onready_callback,
        });
        function krpano_onready_callback(krpano_interface) {
            krpano = krpano_interface;
        }*/
    </script>

</div>

</body>
</html>



