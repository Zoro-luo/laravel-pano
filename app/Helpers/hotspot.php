<?php

/**
 * 热点操作[add edit del]
 * @param $xmlFile
 */
function makeSelectAction($xmlFile)
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);

    //add_scene_items
    $add_scene_items = $vtourDocXml->createElement("action");
    $add_scene_items->setAttribute("name", "add_scene_items");
    $add_scene_items->setAttribute("scope", "local");
    $add_scene_items->nodeValue = "
		for(set(i,0), i LT scene.count, inc(i),
		caller.additem(
		calc('[img src=[dq]' + scene[get(i)].thumburl + '[dq] style=[dq]border:1px solid rgba(255,255,255,0.5);
		width:45px;height:45px;vertical-align:middle;margin-right:180px;[dq]/] '+scene[get(i)].title),
		calc('ajax_change_select('+ scene[get(i)].name +',null,MERGE,BLEND(0.5))') );
		);
    ";


    //ajax_save_hotspot
    $ajax_save_hotspot = $vtourDocXml->createElement("action");
    $ajax_save_hotspot->setAttribute("name", "ajax_save_hotspot");
    $ajax_save_hotspot->setAttribute("type", "Javascript");
    $ajax_save_hotspot->nodeValue = "
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/keepspot',
		data:{'hostName':args[1],'h':args[2], 'v':args[3],
		'sceneName':args[4],'panoId':args[5],'sceneIndex':args[6],'selectName':args[7]},
		success:function(e){
		console.log(e);
		leaf.message('Add Hotspot Succeed!',\"pano\");
		krpano.call(\"loadpano(tour.xml, NULL, MERGE, BLEND(0.1));\");
		krpano.call(\"loadscene(\" + e.sceneName + \", NULL, MERGE, BLEND(0.1));\");
		}
		});
    ";


    //ajax_update_hotspot
    $ajax_update_hotspot = $vtourDocXml->createElement("action");
    $ajax_update_hotspot->setAttribute("name", "ajax_update_hotspot");
    $ajax_update_hotspot->setAttribute("type", "Javascript");
    $ajax_update_hotspot->nodeValue = "
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/ediths',
		data:{'hostName':args[1],'h':args[2], 'v':args[3],
		'sceneName':args[4],'panoId':args[5],'sceneIndex':args[6],'selectName':args[7]},
		success:function(e){
		leaf.message('Update Hotspot Succeed!',\"pano\");
		krpano.call(\"loadpano(tour.xml, NULL, MERGE, BLEND(0.1));\");
		krpano.call(\"loadscene(\" + e.sceneName + \", NULL, MERGE, BLEND(0.1));\");
		}
		});
    ";

    //ajax_delete_hotspot
    $ajax_delete_hotspot = $vtourDocXml->createElement("action");
    $ajax_delete_hotspot->setAttribute("name","ajax_delete_hotspot");
    $ajax_delete_hotspot->setAttribute("type","Javascript");
    $ajax_delete_hotspot->nodeValue ="
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/delspot',
		data:{'hsName':args[1],'panoId':args[2],'sceneIndex':args[3]},
		success:function(e){
		console.log(e);
		leaf.message('Delete Hotspot Succeed!',\"pano\");
		}
		});
    ";

    // ajax_change_select
    $ajax_change_select = $vtourDocXml->createElement("action");
    $ajax_change_select->setAttribute("name","ajax_change_select");
    $ajax_change_select->setAttribute("type","Javascript");
    $ajax_change_select->nodeValue = "
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/changespot',
		data:{'changeName':args[1]},
		success:function(e){
		console.log(e);
		}
		});
    ";

    $krpanoNode->appendChild($add_scene_items);
    $krpanoNode->appendChild($ajax_save_hotspot);
    $krpanoNode->appendChild($ajax_update_hotspot);
    $krpanoNode->appendChild($ajax_delete_hotspot);
    $krpanoNode->appendChild($ajax_change_select);
    $vtourDocXml->save($xmlFile);
}


/**
 * VR 热点/标签 (特效动画) [style/action]
 * @param $xmlFile
 */
function handleHsAnimate($xmlFile){
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);

    // <!--标签style--> point_style_animated
    $point_style_animated = $vtourDocXml->createElement("style");
    $point_style_animated->setAttribute("name", "point_style_animated");
    $point_style_animated->setAttribute("onloaded", "hotspot_animate();add_all_the_time_tooltip_line();add_all_the_time_tooltip_point();");
    $point_style_animated->setAttribute("url", "../../../static/images/move_point.png");
    $point_style_animated->setAttribute("crop", "0|0|20|20");
    $point_style_animated->setAttribute("scale", "2");
    $point_style_animated->setAttribute("framewidth", "20");
    $point_style_animated->setAttribute("frameheight", "20");
    $point_style_animated->setAttribute("frame", "0");
    $point_style_animated->setAttribute("lastframe", "11");

    //<!--热点style--> hotspot_style_animated
    $hotspot_style_animated = $vtourDocXml->createElement("style");
    $hotspot_style_animated->setAttribute("name","hotspot_style_animated");
    $hotspot_style_animated->setAttribute("edge","top");
    $hotspot_style_animated->setAttribute("oy","0");
    $hotspot_style_animated->setAttribute("distorted","false");
    $hotspot_style_animated->setAttribute("onclick","skin_hotspotstyle_click();");
    $hotspot_style_animated->setAttribute("onloaded","hotspot_animate();add_all_the_time_tooltip_hotspot();");
    $hotspot_style_animated->setAttribute("scale","1.5");
    $hotspot_style_animated->setAttribute("url","../../../static/images/move_hotspot.png");
    $hotspot_style_animated->setAttribute("crop","0|0|37|37");
    $hotspot_style_animated->setAttribute("framewidth","35");
    $hotspot_style_animated->setAttribute("frameheight","35");
    $hotspot_style_animated->setAttribute("frame","0");
    $hotspot_style_animated->setAttribute("lastframe","9");

    // <!--热点动画效果-->  hotspot_animate
    $hotspot_animate = $vtourDocXml->createElement("action");
    $hotspot_animate ->setAttribute("name","hotspot_animate");
    $hotspot_animate->nodeValue = "inc(frame,1,get(lastframe),0); mul(ypos,frame,frameheight);
    txtadd(crop,'0|',get(ypos),'|',get(framewidth),'|',get(frameheight));
    delayedcall(0.09, if(loaded, hotspot_animate() ) );";

    //  <!-- 便签右上角显示红× -->   add_all_the_time_tooltip_error
    $add_all_the_time_tooltip_error = $vtourDocXml->createElement("action");
    $add_all_the_time_tooltip_error->setAttribute("name","add_all_the_time_tooltip_error");
    $add_all_the_time_tooltip_error->nodeValue = "txtadd(linename, 'toolstip_', get(name));addplugin(get(linename));
    txtadd(plugin[get(linename)].parent, 'hotspot[',get(name),']');set(plugin[get(linename)].keep,true);
    set(plugin[get(linename)].url,'%SWFPATH%/../../../static/images/error_max.png');set(plugin[get(linename)].align,leftttop);
    set(plugin[get(linename)].x,106);set(plugin[get(linename)].y,-14);set(plugin[get(linename)].scale,0.8);
    set(plugin[get(linename)].visible,true);";



    //  <!-- 便签右边显示横线 -->  add_all_the_time_tooltip_line
    $add_all_the_time_tooltip_line = $vtourDocXml->createElement("action");
    $add_all_the_time_tooltip_line->setAttribute("name","add_all_the_time_tooltip_line");
    $add_all_the_time_tooltip_line->nodeValue = "txtadd(lineaeName, 'toolstip_', get(name));addplugin(get(lineaeName));
    txtadd(plugin[get(lineaeName)].parent, 'hotspot[',get(name),']');set(plugin[get(lineaeName)].url,'%SWFPATH%/plugins/textfield.swf');
    set(plugin[get(lineaeName)].align,right);set(plugin[get(lineaeName)].edge,left);set(plugin[get(lineaeName)].x,5);
    set(plugin[get(lineaeName)].y,0);set(plugin[get(lineaeName)].width,35);set(plugin[get(lineaeName)].height,0.8);
    set(plugin[get(lineaeName)].bgcolor,0x002E343A);set(plugin[get(lineaeName)].background,true);set(plugin[get(lineaeName)].border,true);
    set(plugin[get(lineaeName)].bordercolor,0x00FFFFFF);set(plugin[get(lineaeName)].borderwidth,0.5);set(plugin[get(lineaeName)].enabled,false);";

    // <!-- 便签右边显示文字 --> add_all_the_time_tooltip_point
    $add_all_the_time_tooltip_point = $vtourDocXml->createElement("action");
    $add_all_the_time_tooltip_point->setAttribute("name","add_all_the_time_tooltip_point");
    $add_all_the_time_tooltip_point->nodeValue = "txtadd(tooltipname, 'tooltip_', get(name));addplugin(get(tooltipname));
        txtadd(plugin[get(tooltipname)].parent, 'hotspot[',get(name),']');set(plugin[get(tooltipname)].url,'%SWFPATH%/plugins/textfield.swf');
        set(plugin[get(tooltipname)].align,right);set(plugin[get(tooltipname)].edge,left);set(plugin[get(tooltipname)].x,-30);
        set(plugin[get(tooltipname)].y,0);set(plugin[get(tooltipname)].autowidth,true);set(plugin[get(tooltipname)].autoheight,true);
        set(plugin[get(tooltipname)].bgcolor,0x002E343A);set(plugin[get(tooltipname)].background,true);set(plugin[get(tooltipname)].border,true);
        set(plugin[get(tooltipname)].bordercolor,0x00FFFFFF);set(plugin[get(tooltipname)].borderalpha,2.0);set(plugin[get(tooltipname)].borderwidth,2.0);
        set(plugin[get(tooltipname)].bgalpha,0.5);set(plugin[get(tooltipname)].bgroundedge,18);set(plugin[get(tooltipname)].enabled,false);
        set(plugin[get(tooltipname)].css,'text-align:center; color:#FFFFFF; padding:4px 15px; font-family:Arial;font-size:14px;');
        set(plugin[get(tooltipname)].textshadow,1);set(plugin[get(tooltipname)].textshadowrange,6.0);
        set(plugin[get(tooltipname)].textshadowangle,90);copy(plugin[get(tooltipname)].html,hotspot[get(name)].tooltip);";

    // <!-- 热点下方显示文字 --> add_all_the_time_tooltip_hotspot
    $add_all_the_time_tooltip_hotspot = $vtourDocXml->createElement("action");
    $add_all_the_time_tooltip_hotspot->setAttribute("name","add_all_the_time_tooltip_hotspot");
    $add_all_the_time_tooltip_hotspot->nodeValue = "txtadd(tooltipname, 'tooltip_', get(name));addplugin(get(tooltipname));
        txtadd(plugin[get(tooltipname)].parent, 'hotspot[',get(name),']');set(plugin[get(tooltipname)].url,'%SWFPATH%/plugins/textfield.swf');
        set(plugin[get(tooltipname)].align,bottom);set(plugin[get(tooltipname)].edge,bottom);set(plugin[get(tooltipname)].x,0);
        set(plugin[get(tooltipname)].y,-30);set(plugin[get(tooltipname)].autowidth,true);set(plugin[get(tooltipname)].autoheight,true);
        set(plugin[get(tooltipname)].bgcolor,0x002E343A);set(plugin[get(tooltipname)].background,true);set(plugin[get(tooltipname)].border,false);
        set(plugin[get(tooltipname)].bgalpha,0.6);set(plugin[get(tooltipname)].bgroundedge,5);
        set(plugin[get(tooltipname)].css,'text-align:center; color:#FFFFFF; padding:4px 6px; font-family:Arial; font-weight:bold;font-size:14px;');
        set(plugin[get(tooltipname)].textshadow,1);set(plugin[get(tooltipname)].textshadowrange,6.0);set(plugin[get(tooltipname)].textshadowangle,90);
        copy(plugin[get(tooltipname)].html,hotspot[get(name)].tooltip);set(plugin[get(tooltipname)].enabled,false);";


    $krpanoNode->appendChild($point_style_animated);
    $krpanoNode->appendChild($hotspot_style_animated);
    $krpanoNode->appendChild($hotspot_animate);
    $krpanoNode->appendChild($add_all_the_time_tooltip_error);
    $krpanoNode->appendChild($add_all_the_time_tooltip_line);
    $krpanoNode->appendChild($add_all_the_time_tooltip_point);
    $krpanoNode->appendChild($add_all_the_time_tooltip_hotspot);

    $vtourDocXml->save($xmlFile);

}
