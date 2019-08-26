<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
          href="{{asset('public/static/AdminLTE')}}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <script src="{{asset('public/static/pano/js')}}/tour.js"></script>
    <script src="{{asset('public/static/AdminLTE')}}/bower_components/jquery/dist/jquery.min.js"></script>

    <script src="{{asset('public/static/leaf')}}/libs/art-dialog/dist/dialog-min.js"></script>
    <link href="{{asset('public/static/leaf')}}/libs/art-dialog/css/ui-dialog.css" rel="stylesheet"/>
    <script src="{{asset('public/static/leaf')}}/js/leaf.js"></script>
    <link href="{{asset('public/static/leaf')}}/css/leaf.css" rel="stylesheet"/>
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

        .button_caninater {
            background-color: #EEEEEE;
            /* text-align: center;*/
        }

        .button {
            display: inline-block;
            border: 1px solid gray;
            border-radius: 4px;
            cursor: pointer;
            padding: 2px 6px;
            margin: 0;
            user-select: none;
            -moz-user-select: none;
            font-size: 13px;
            color: #00caff;
        }

        .button:hover {
            background-color: #e4eef3;
            color: #f59e00;
        }

        #currentview {
            color: #00caff;
            font-size: 13px;
        }
    </style>

</head>
<body>

<div class="button_caninater">
    <button class="button but1" onclick="start_up();">Set as startup view</button>
    <button class="button but2" onclick="add_hs();">Add hotspot</button>
    <button class="button but3" onclick="edit_hs();">Edit hotspot</button>
    <button class="button but3" onclick="get_current_view();">Get current view</button>
    <span id="currentview" style="padding-left:8px; font-family:monospace;"></span>
</div>


<div id="pano" style="width:100%; height:96%;">

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
        var xmlPath = "{{asset('storage/panos/').'/'.$panoId }}/vtour/tour.xml";
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
    </script>

</div>


<script>

    //设置启动视角
    function start_up() {

        if (krpano) {
            var sceneIndex = krpano.get("scene[get(xml.scene)].index");
            var panoId = "{{$panoId}}";
            var sceneName = krpano.get("xml.scene");

            var hlookat = krpano.get("view.hlookat").toFixed(3);
            var vlookat = krpano.get("view.vlookat").toFixed(3);

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{url('admin/startup')}}",
                type: "POST",
                data: {"sceneIndex": sceneIndex, "panoId": panoId, "hlookat": hlookat, "vlookat": vlookat},
                success: function (e) {
                    leaf.message('初始视角设置成功!', "pano");
                    krpano.call("lookat(" + e.h + "," + e.v + ",120)");
                    krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                    krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");
                }
            })
        }

    }

    //编辑热点
    function edit_hs() {
        if (krpano) {
            var sceneIndex = krpano.get("scene[get(xml.scene)].index");
            var panoId = "{{$panoId}}";
            var sceneName = krpano.get("xml.scene");
            //ajax 更新热点新增拖拽
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{url('admin/editspot')}}",
                type: "POST",
                data: {"sceneIndex": sceneIndex, "panoId": panoId, "sceneName": sceneName},
                success: function (e) {
                    leaf.message('可以拖拽编辑热点啦!', "pano");
                    krpano.call("looktohotspot(" + e.hostName + ",120,smooth(100,50,20))");
                    krpano.call("loadpano(" + xmlPath + ", NULL, MERGE, BLEND(0.1));");
                    krpano.call("loadscene(" + sceneName + ", NULL, MERGE, BLEND(0.1));");
                }
            });
        }
    }

    //添加热点
    function add_hs() {
        if (krpano) {
            var h = krpano.get("view.hlookat").toFixed(3);
            var v = krpano.get("view.vlookat").toFixed(3);
            var sceneName = krpano.get("xml.scene");
            var sceneIndex = krpano.get("scene[get(xml.scene)].index");
            var panoId = "{{$panoId}}";

            console.log(h);
            console.log(v);
            var hs_name = sceneName + '_' + Math.abs((Date.now() + Math.random()) | 0);

            krpano.call("addhotspot(" + hs_name + ")");
            krpano.set("hotspot[" + hs_name + "].url", "{{asset('storage/panos/').'/'.$panoId}}/vtour/skin/vtourskin_hotspot.png");
            krpano.set("hotspot[" + hs_name + "].ath", h);
            krpano.set("hotspot[" + hs_name + "].atv", v);
            krpano.set("hotspot[" + hs_name + "].zoom", "true");
            krpano.set("hotspot[" + hs_name + "].scale", "0.45");
            krpano.set("hotspot[" + hs_name + "].ondown", "draghotspot();");

            if (krpano.get("device.html5")) {
                krpano.set("hotspot[" + hs_name + "].onclick", function (hs) {

                    var mx = krpano.get("mouse.x");
                    var my = krpano.get("mouse.y");
                    var pnt = krpano.screentosphere(mx, my);
                    var hh = pnt.x.toFixed(3);
                    var vv = pnt.y.toFixed(3);

                    var first_elect_name = krpano.get("scene[0].name");
                    //var first_elect_name = krpano.get("scene[get(xml.scene)].name");

                    handleCombobox(hs, hh, vv, sceneName, panoId, sceneIndex, first_elect_name);

                }.bind(null, hs_name));
            } else {
                krpano.set("hotspot[" + hs_name + "].onclick", "js( alert(calc('hotspot \"' + name + '\" clicked')) );");
            }
        }
    }

    //编辑后点击触发下拉选择框
    function handleTackAction(hs) {

        var panoId = "{{$panoId}}";
        var new_h = krpano.get("view.hlookat").toFixed(3);
        var new_v = krpano.get("view.vlookat").toFixed(3);
        var sceneName = krpano.get("xml.scene");
        var sceneIndex = krpano.get("scene[get(xml.scene)].index");
        var first_elect_name = krpano.get("scene[0].name");

        var mx = krpano.get("mouse.x");
        var my = krpano.get("mouse.y");
        var pnt = krpano.screentosphere(mx, my);
        var hh = pnt.x.toFixed(3);
        var vv = pnt.y.toFixed(3);

        if (hs) {
            handleCombobox(hs, hh, vv, sceneName, panoId, sceneIndex, first_elect_name, sign = "edit");
        }
    }

    //生成下拉选择窗
    function handleCombobox(hs, new_h, new_v, sceneName, panoId, sceneIndex, first_elect_name, sign = "default") {
        //  layer[cbde_container]
        krpano.call("addlayer(cbde_container)");
        krpano.set("layer[cbde_container].align", "center");
        krpano.set("layer[cbde_container].width", '350');
        krpano.set("layer[cbde_container].height", '200');
        krpano.set("layer[cbde_container].url", '%SWFPATH%/plugins/textfield.swf');
        krpano.set("layer[cbde_container].backgroundcolor", '0x00FFFFFF');
        //layer[cbde_title]
        krpano.call("addlayer(cbde_title)");
        krpano.set("layer[cbde_title].align", "center");
        krpano.set("layer[cbde_title].y", "-74");
        krpano.set("layer[cbde_title].url", "%SWFPATH%/plugins/textfield.swf");
        krpano.set("layer[cbde_title].css", "text-align:center;color:#000000;font-family:Arial;font-weight:bold;font-size:14px;");
        krpano.set("layer[cbde_title].html", "Edit Hotspot");
        //layer[cbde_title]
        krpano.call("addlayer(cbde_hint)");
        krpano.set("layer[cbde_hint].align", "center");
        krpano.set("layer[cbde_hint].x", "-90");
        krpano.set("layer[cbde_hint].y", "-40");
        krpano.set("layer[cbde_hint].url", "%SWFPATH%/plugins/textfield.swf");
        krpano.set("layer[cbde_hint].css", "text-align:center;color:#000000;font-size:12px;");
        krpano.set("layer[cbde_hint].html", "Select Hotspot Target");
        //layer[delete_button]
        krpano.call("addlayer(delete_button)");
        krpano.set("layer[delete_button].align", "center");
        krpano.set("layer[delete_button].y", "60");
        krpano.set("layer[delete_button].width", "80");
        krpano.set("layer[delete_button].height", "30");
        krpano.set("layer[delete_button].url", "%SWFPATH%/plugins/textfield.swf");
        krpano.set("layer[delete_button].css", "text-align:center;color:#000000;margin-top:8px;font-size:12px;");
        krpano.set("layer[delete_button].shadow", "2");
        krpano.set("layer[delete_button].html", "Delete");
        krpano.set("layer[delete_button].onover", "set(layer[delete_button].backgroundcolor,'0x00F7E7C9');");
        krpano.set("layer[delete_button].onout", "set(layer[delete_button].backgroundcolor,'0x00FFFFFF')");

        if (sign == "default") {
            krpano.set("layer[delete_button].onclick", "removelayer(cbde_container);removelayer(cbde_title);" +
                "removelayer(cbde_hint);removelayer(save_button);removelayer(delete_button);removelayer(cancel_button);" +
                "removeComboboxLayer(cbdesigns);removehotspot(" + hs + "));");
        } else if (sign == "edit") {
            krpano.set("layer[delete_button].onclick", "ajax_delete_hotspot(" + hs + "," + panoId + "," + sceneIndex + "); removelayer(cbde_container);removelayer(cbde_title);" +
                "removelayer(cbde_hint);removelayer(save_button);removelayer(delete_button);removelayer(cancel_button);" +
                "removeComboboxLayer(cbdesigns);removehotspot(" + hs + "));");
        }

        //layer[cancel_button]
        krpano.call("addlayer(cancel_button)");
        krpano.set("layer[cancel_button].align", "center");
        krpano.set("layer[cancel_button].x", "100");
        krpano.set("layer[cancel_button].y", "60");
        krpano.set("layer[cancel_button].width", "80");
        krpano.set("layer[cancel_button].height", "30");
        krpano.set("layer[cancel_button].url", "%SWFPATH%/plugins/textfield.swf");
        krpano.set("layer[cancel_button].css", "text-align:center;color:#000000;margin-top:8px;font-size:12px;");
        krpano.set("layer[cancel_button].shadow", "2");
        krpano.set("layer[cancel_button].html", "Cancel");
        krpano.set("layer[cancel_button].onover", "set(layer[cancel_button].backgroundcolor,'0x00F7E7C9');");
        krpano.set("layer[cancel_button].onout", "set(layer[cancel_button].backgroundcolor,'0x00FFFFFF')");
        krpano.set("layer[cancel_button].onclick", "removelayer(cbde_container);removelayer(cbde_title);removelayer(cbde_hint);" +
            "removelayer(save_button);removelayer(delete_button);removelayer(cancel_button);removeComboboxLayer(cbdesigns);");


        //layer[save_button]
        krpano.call("addlayer(save_button)");
        krpano.set("layer[save_button].align", "center");
        krpano.set("layer[save_button].x", "-100");
        krpano.set("layer[save_button].y", "60");
        krpano.set("layer[save_button].width", "80");
        krpano.set("layer[save_button].height", "30");
        krpano.set("layer[save_button].url", "%SWFPATH%/plugins/textfield.swf");
        krpano.set("layer[save_button].css", "text-align:center;color:#000000;margin-top:8px;font-size:12px;");
        krpano.set("layer[save_button].shadow", "2");
        krpano.set("layer[save_button].html", "Save");
        krpano.set("layer[save_button].onover", "set(layer[save_button].backgroundcolor,'0x00F7E7C9');");
        krpano.set("layer[save_button].onout", "set(layer[save_button].backgroundcolor,'0x00FFFFFF')");

        if (sign == "default") {
            krpano.set("layer[save_button].onclick", "removelayer(cbde_container);removelayer(cbde_title);" +
                "removelayer(cbde_hint);removelayer(save_button);removelayer(delete_button);removelayer(cancel_button);" +
                "removeComboboxLayer(cbdesigns);" +
                "ajax_save_hotspot(" + hs + "," + new_h + "," + new_v + "," + sceneName + "," + panoId + "," + sceneIndex + "," + first_elect_name + ");"
            );
        } else if (sign == "edit") {
            krpano.set("layer[save_button].onclick", "removelayer(cbde_container);removelayer(cbde_title);" +
                "removelayer(cbde_hint);removelayer(save_button);removelayer(delete_button);removelayer(cancel_button);" +
                "removeComboboxLayer(cbdesigns);" +
                "ajax_update_hotspot(" + hs + "," + new_h + "," + new_v + "," + sceneName + "," + panoId + "," + sceneIndex + "," + first_elect_name + ");"
            );
        }

        //addComboboxLayer[cbdesigns]
        krpano.call("addComboboxLayer(cbdesigns,default)");
        krpano.set("layer[cbdesigns].align", "center");
        krpano.set("layer[cbdesigns].onloaded", "add_scene_items");

    }

    //获取当前视图
    function get_current_view() {
        if (krpano) {
            var hlookat = krpano.get("view.hlookat");
            var vlookat = krpano.get("view.vlookat");
            var fov = krpano.get("view.fov");
            document.getElementById("currentview").innerHTML =
                'hlookat="' + hlookat.toFixed(2) + '" ' +
                'vlookat="' + vlookat.toFixed(2) + '" ' +
                'fov="' + fov.toFixed(2) + '" ';
        }
    }

</script>
</body>
</html>



